<?php
/**
 * MaiCafe - Laravel Debug Script
 * Upload to hosting root and visit it to see the actual Laravel error.
 * DELETE after troubleshooting!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo '<!DOCTYPE html>
<html>
<head>
<title>MaiCafe - Debug</title>
<style>
  body { font-family: monospace; background: #1a1a2e; color: #eee; margin: 0; padding: 20px; }
  h2 { color: #00d4ff; border-bottom: 1px solid #333; padding-bottom: 8px; }
  .box { background: #16213e; border: 1px solid #333; border-radius: 6px; padding: 16px; margin: 12px 0; }
  .ok   { color: #00ff88; }
  .fail { color: #ff4444; }
  .warn { color: #ffaa00; }
  pre { background: #0f0f1a; color: #ff6666; padding: 16px; border-radius: 6px; overflow-x: auto; white-space: pre-wrap; word-break: break-all; font-size: 13px; }
  pre.log { color: #ccc; }
  table { width: 100%; border-collapse: collapse; }
  td { padding: 4px 10px; border-bottom: 1px solid #222; font-size: 13px; }
  td:first-child { color: #888; width: 220px; }
</style>
</head>
<body>
<h1 style="color:#00d4ff;">MaiCafe Debug Panel</h1>
';

// ──────────────────────────────────────────────
// 1. SERVER INFO
// ──────────────────────────────────────────────
echo '<h2>1. Server Info</h2><div class="box"><table>';
$info = [
    'PHP Version'        => PHP_VERSION,
    'SAPI'               => php_sapi_name(),
    'Server Software'    => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
    'Document Root'      => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
    'Script Path'        => __FILE__,
    'Working Directory'  => getcwd(),
    'memory_limit'       => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
];
foreach ($info as $k => $v) {
    echo "<tr><td>$k</td><td>" . htmlspecialchars((string)$v) . "</td></tr>";
}
echo '</table></div>';

// ──────────────────────────────────────────────
// 2. KEY FILES CHECK
// ──────────────────────────────────────────────
echo '<h2>2. Key Files</h2><div class="box">';
$files = [
    'index.php'                     => __DIR__ . '/index.php',
    '.htaccess'                     => __DIR__ . '/.htaccess',
    '.env'                          => __DIR__ . '/.env',
    'core/vendor/autoload.php'      => __DIR__ . '/core/vendor/autoload.php',
    'core/bootstrap/app.php'        => __DIR__ . '/core/bootstrap/app.php',
    'core/storage/logs/ (dir)'      => __DIR__ . '/core/storage/logs',
    'core/storage/framework/ (dir)' => __DIR__ . '/core/storage/framework',
    'core/bootstrap/cache/ (dir)'   => __DIR__ . '/core/bootstrap/cache',
];
foreach ($files as $label => $path) {
    $exists   = file_exists($path);
    $perm     = $exists ? substr(sprintf('%o', fileperms($path)), -4) : '';
    $writable = ($exists && is_dir($path)) ? is_writable($path) : null;
    $status   = $exists
        ? '<span class="ok">&#10003; exists</span>'
        : '<span class="fail">&#10007; MISSING</span>';
    $wstatus  = '';
    if ($writable === true)  $wstatus = ' <span class="ok">[writable]</span>';
    if ($writable === false) $wstatus = ' <span class="fail">[NOT writable]</span>';
    echo "<div>$status &nbsp; $label <small style='color:#555'>$perm</small>$wstatus</div>";
}
echo '</div>';

// ──────────────────────────────────────────────
// 3. .ENV PARSED VALUES (safe subset)
// ──────────────────────────────────────────────
echo '<h2>3. .env Values</h2><div class="box">';
$env     = [];
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            [$k, $v] = explode('=', $line, 2);
            $env[trim($k)] = trim($v);
        }
    }
    $show = ['APP_NAME','APP_ENV','APP_DEBUG','APP_URL',
             'DB_CONNECTION','DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME'];
    echo '<table>';
    foreach ($show as $key) {
        $val = isset($env[$key]) ? htmlspecialchars($env[$key]) : '<span class="fail">NOT SET</span>';
        echo "<tr><td>$key</td><td>$val</td></tr>";
    }
    $keyVal = $env['APP_KEY'] ?? '';
    if (empty($keyVal)) {
        echo '<tr><td>APP_KEY</td><td><span class="fail">&#10007; EMPTY — run generate_key.php</span></td></tr>';
    } else {
        echo '<tr><td>APP_KEY</td><td><span class="ok">&#10003; SET</span> (' . htmlspecialchars(substr($keyVal, 0, 20)) . '...)</td></tr>';
    }
    echo '</table>';
} else {
    echo '<span class="fail">&#10007; .env file not found!</span>';
}
echo '</div>';

// ──────────────────────────────────────────────
// 4. DATABASE CONNECTION TEST
// ──────────────────────────────────────────────
echo '<h2>4. Database Connection</h2><div class="box">';
if (!empty($env)) {
    $host = $env['DB_HOST'] ?? 'localhost';
    $port = $env['DB_PORT'] ?? '3306';
    $db   = $env['DB_DATABASE'] ?? '';
    $user = $env['DB_USERNAME'] ?? '';
    $pass = $env['DB_PASSWORD'] ?? '';

    if ($db && $user) {
        try {
            $pdo = new PDO(
                "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
                $user, $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
            );
            $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
            echo '<span class="ok">&#10003; Connected to: ' . htmlspecialchars($db) . '</span><br>';
            if ($tables) {
                echo '<span class="ok">&#10003; Tables (' . count($tables) . '):</span> ';
                echo '<small style="color:#888">' . htmlspecialchars(implode(', ', $tables)) . '</small>';
            } else {
                echo '<span class="warn">&#9888; No tables found — run migrations</span>';
            }
        } catch (PDOException $e) {
            echo '<span class="fail">&#10007; DB Error: ' . htmlspecialchars($e->getMessage()) . '</span>';
        }
    } else {
        echo '<span class="warn">&#9888; DB_DATABASE or DB_USERNAME not set</span>';
    }
} else {
    echo '<span class="warn">&#9888; .env not loaded</span>';
}
echo '</div>';

// ──────────────────────────────────────────────
// 5. LARAVEL BOOTSTRAP — CAPTURE THE REAL ERROR
// ──────────────────────────────────────────────
echo '<h2>5. Laravel Bootstrap (Actual Error)</h2><div class="box">';

$autoload = __DIR__ . '/core/vendor/autoload.php';
$appFile  = __DIR__ . '/core/bootstrap/app.php';

if (!file_exists($autoload)) {
    echo '<span class="fail">&#10007; vendor/autoload.php missing — run composer install</span>';
} elseif (!file_exists($appFile)) {
    echo '<span class="fail">&#10007; core/bootstrap/app.php missing</span>';
} else {
    set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    });

    $capturedOutput = '';
    ob_start();

    try {
        require $autoload;
        echo '<span class="ok">&#10003; Autoloader loaded</span><br>';

        $app = require_once $appFile;
        echo '<span class="ok">&#10003; app.php loaded</span><br>';

        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        echo '<span class="ok">&#10003; HTTP Kernel created</span><br>';

        $request  = Illuminate\Http\Request::capture();
        $response = $kernel->handle($request);

        $code = $response->getStatusCode();
        if ($code >= 500) {
            echo '<span class="fail">&#10007; App returned HTTP ' . $code . '</span><br>';
            echo '<pre>' . htmlspecialchars(substr($response->getContent(), 0, 8000)) . '</pre>';
        } elseif ($code >= 400) {
            echo '<span class="warn">&#9888; App returned HTTP ' . $code . '</span>';
        } else {
            echo '<span class="ok">&#10003; App responded HTTP ' . $code . ' — Laravel is working!</span>';
        }

        $kernel->terminate($request, $response);
        restore_error_handler();
        ob_end_clean();

    } catch (Throwable $e) {
        restore_error_handler();
        $capturedOutput = ob_get_clean();

        echo '<span class="fail">&#10007; Exception: ' . htmlspecialchars(get_class($e)) . '</span><br>';
        echo '<pre>';
        echo htmlspecialchars($e->getMessage()) . "\n\n";
        echo 'File: ' . htmlspecialchars($e->getFile()) . ' (line ' . $e->getLine() . ")\n\n";
        echo "Stack Trace:\n" . htmlspecialchars($e->getTraceAsString());
        echo '</pre>';

        if ($capturedOutput) {
            echo '<br><strong>Output before crash:</strong>';
            echo '<pre class="log">' . htmlspecialchars($capturedOutput) . '</pre>';
        }
    }
}
echo '</div>';

// ──────────────────────────────────────────────
// 6. LARAVEL LOG (last 100 lines)
// ──────────────────────────────────────────────
echo '<h2>6. Laravel Log (last 100 lines)</h2><div class="box">';
$logFile = __DIR__ . '/core/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $size = round(filesize($logFile) / 1024, 1);
    echo '<small style="color:#888">Path: ' . htmlspecialchars($logFile) . ' (' . $size . ' KB)</small><br><br>';
    $lines = file($logFile);
    $tail  = array_slice($lines, -100);
    if ($tail) {
        echo '<pre class="log">' . htmlspecialchars(implode('', $tail)) . '</pre>';
    } else {
        echo '<span class="warn">Log file is empty.</span>';
    }
} else {
    echo '<span class="warn">&#9888; Log file not found yet (normal before first Laravel request)</span>';
}
echo '</div>';

// ──────────────────────────────────────────────
// 7. PHP ERROR LOG
// ──────────────────────────────────────────────
echo '<h2>7. PHP Error Log</h2><div class="box">';
$phpLog = ini_get('error_log');
if ($phpLog && file_exists($phpLog)) {
    $lines = file($phpLog);
    $tail  = array_slice($lines, -50);
    echo '<small style="color:#888">Path: ' . htmlspecialchars($phpLog) . '</small><br><br>';
    echo '<pre>' . htmlspecialchars(implode('', $tail)) . '</pre>';
} else {
    echo '<span class="warn">&#9888; PHP error log not accessible (path: '
        . htmlspecialchars($phpLog ?: 'not configured') . ')</span>';
}
echo '</div>';

echo '<p style="color:#ff4444;margin-top:30px;"><strong>&#9888; DELETE this file after troubleshooting!</strong></p>';
echo '</body></html>';
