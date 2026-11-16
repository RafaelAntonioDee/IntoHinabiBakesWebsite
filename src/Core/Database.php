<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database {
    private static ?PDO $connection = null;
    private static ?string $lastError = null;

    /**
     * Get or create PDO instance
     */
    public static function getConnection(): PDO {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $config = require dirname(__DIR__, 2) . '/config/database.php';
        $default = $config['default'] ?? 'supabase';
        $settings = $config['connections'][$default] ?? null;

        if (!$settings) {
            throw new RuntimeException("Database connection [{$default}] is not defined.");
        }

        $driver = $settings['driver'];

        try {
            if ($driver === 'pgsql') {
                $host = $settings['host'];
                $port = $settings['port'];
                $dbname = $settings['database'];
                $sslmode = $settings['sslmode'] ?? 'require';
                
                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode={$sslmode}";
                self::$connection = new PDO(
                    $dsn,
                    $settings['username'],
                    $settings['password'],
                    $settings['options'] ?? []
                );
            } elseif ($driver === 'mysql') {
                $host = $settings['host'];
                $port = $settings['port'];
                $dbname = $settings['database'];
                $charset = $settings['charset'] ?? 'utf8mb4';

                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
                self::$connection = new PDO(
                    $dsn,
                    $settings['username'],
                    $settings['password'],
                    $settings['options'] ?? []
                );
            } else {
                throw new RuntimeException("Unsupported database driver [{$driver}].");
            }

            return self::$connection;
        } catch (PDOException $e) {
            self::$lastError = $e->getMessage();
            
            $debug = env('APP_DEBUG', true);
            $helpMsg = ($default === 'supabase')
                ? "Could not connect to Supabase PostgreSQL.\nMake sure your SUPABASE_DB_HOST, SUPABASE_DB_PASSWORD, and SUPABASE_DB_PORT in .env are correct."
                : "Could not connect to local MySQL.\nEnsure MySQL is started in XAMPP Control Panel and database '{$settings['database']}' exists.";

            if ($debug) {
                throw new RuntimeException("{$helpMsg}\n\nPDO Error: " . $e->getMessage(), (int)$e->getCode(), $e);
            } else {
                throw new RuntimeException("Database service is currently unavailable. Please check configuration.");
            }
        }
    }

    /**
     * Get last connection error (if any)
     */
    public static function getLastError(): ?string {
        return self::$lastError;
    }

    /**
     * Test connection without throwing fatal exception
     */
    public static function testConnection(): array {
        try {
            $pdo = self::getConnection();
            $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            $version = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
            return [
                'success' => true,
                'driver'  => $driver,
                'version' => $version,
                'message' => "Successfully connected to {$driver} (Version: {$version})",
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
