<?php

namespace App\Core;

use PDO;
use Throwable;

abstract class Model {
    protected ?PDO $db = null;

    public function __construct() {
        try {
            $this->db = Database::getConnection();
        } catch (Throwable $e) {
            $this->db = null;
        }
    }

    /**
     * Get active database connection or throw if unavailable
     */
    protected function getDb(): PDO {
        if ($this->db === null) {
            $this->db = Database::getConnection();
        }
        return $this->db;
    }
}
