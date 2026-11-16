<?php

require_once dirname(__DIR__) . '/config/app.php';

use App\Core\Database;

$testResult = Database::testConnection();
$activeConn = env('DB_CONNECTION', 'supabase');

$hasPgsql = extension_loaded('pdo_pgsql');
$hasMysql = extension_loaded('pdo_mysql');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Diagnostic - Sweet Cookie Oven</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-8 border border-gray-200">
        <h1 class="text-2xl font-bold text-amber-900 mb-2">Database Connection Diagnostic</h1>
        <p class="text-gray-600 text-sm mb-6">Tests your connection to <strong>Supabase (PostgreSQL)</strong> or local <strong>MySQL</strong>.</p>

        <!-- PHP Extension Status -->
        <div class="mb-6 space-y-2">
            <h2 class="text-xs uppercase font-bold text-gray-500 tracking-wider">PHP Drivers Status</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 rounded-lg border <?= $hasPgsql ? 'bg-green-50 border-green-200 text-green-900' : 'bg-red-50 border-red-200 text-red-900' ?>">
                    <span class="font-bold">pdo_pgsql (Supabase):</span> 
                    <span><?= $hasPgsql ? '✓ Enabled' : '✗ Disabled' ?></span>
                </div>
                <div class="p-4 rounded-lg border <?= $hasMysql ? 'bg-green-50 border-green-200 text-green-900' : 'bg-red-50 border-red-200 text-red-900' ?>">
                    <span class="font-bold">pdo_mysql (MySQL):</span> 
                    <span><?= $hasMysql ? '✓ Enabled' : '✗ Disabled' ?></span>
                </div>
            </div>
        </div>

        <!-- Active Connection Configuration -->
        <div class="mb-6 p-4 bg-amber-50 rounded-lg border border-amber-200 text-sm">
            <span class="font-bold text-amber-950">Active DB_CONNECTION in .env:</span> 
            <span class="px-2 py-0.5 bg-amber-200 font-mono text-amber-900 rounded font-semibold"><?= e($activeConn) ?></span>
        </div>

        <!-- Connection Test Result -->
        <div class="mb-6">
            <h2 class="text-xs uppercase font-bold text-gray-500 tracking-wider mb-2">Connection Status</h2>
            <?php if ($testResult['success']): ?>
                <div class="p-4 bg-green-100 border border-green-300 text-green-900 rounded-lg">
                    <p class="font-bold text-lg">🎉 Connection Successful!</p>
                    <p class="text-sm mt-1"><?= e($testResult['message']) ?></p>
                </div>
            <?php else: ?>
                <div class="p-4 bg-red-100 border border-red-300 text-red-900 rounded-lg">
                    <p class="font-bold text-lg">⚠️ Connection Failed</p>
                    <p class="text-sm font-mono mt-2 break-all"><?= e($testResult['message']) ?></p>
                </div>
                
                <div class="mt-4 p-4 bg-gray-100 rounded-lg text-xs space-y-2 text-gray-700">
                    <p class="font-bold text-gray-900">How to fix:</p>
                    <?php if ($activeConn === 'supabase'): ?>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Open your <code>.env</code> file.</li>
                            <li>Set <code>SUPABASE_DB_HOST</code> to your project host from Supabase (Project Settings &rarr; Database &rarr; Connection info).</li>
                            <li>Set <code>SUPABASE_DB_PASSWORD</code> to your Supabase database password.</li>
                            <li>Make sure port <code>5432</code> or <code>6543</code> is not blocked by your firewall.</li>
                        </ul>
                    <?php else: ?>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Open XAMPP Control Panel and start the <strong>MySQL</strong> service.</li>
                            <li>Make sure database <code>sweet_cookie_db</code> exists in phpMyAdmin.</li>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="pt-4 border-t border-gray-200 flex justify-between items-center text-sm">
            <a href="<?= url('/') ?>" class="text-amber-800 font-semibold hover:underline">&larr; Return to Website</a>
            <a href="test_db.php" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-md font-semibold text-xs">Re-run Test</a>
        </div>
    </div>
</body>
</html>
