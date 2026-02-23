<?php
/**
 * MaiCafe - Storage Setup Script
 * Upload to hosting root and visit in browser.
 * DELETE after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<!DOCTYPE html>
<html>
<head>
<title>MaiCafe - Storage Setup</title>
<style>
  body { font-family: monospace; background: #1a1a2e; color: #eee; margin: 0; padding: 20px; }
  h2 { color: #00d4ff; border-bottom: 1px solid #333; padding-bottom: 8px; }
  .box { background: #16213e; border: 1px solid #333; border-radius: 6px; padding: 16px; margin: 12px 0; }
  .ok   { color: #00ff88; }
  .fail { color: #ff4444; }
  .warn { color: #ffaa00; }
  pre { background: #0f0f1a; color: #ccc; padding: 16px; border-radius: 6px; font-size: 13px; white-space: pre-wrap; }
</style>
</head>
<body>
<h1 style="color:#00d4ff;">MaiCafe - Storage Setup</h1>
';

$webroot    = __DIR__;
$corePath   = $webroot . '/core';
$storageSrc = $corePath . '/storage/app/public';   // real folder
$storageLnk = $webroot . '/storage';               // symlink at webroot

// ──────────────────────────────────────────────
// STEP 1: Ensure core/storage/app/public exists
// ──────────────────────────────────────────────
echo '<h2>Step 1: Verify Storage Directories</h2><div class="box">';
$dirs = [
    $corePath . '/storage',
    $corePath . '/storage/app',
    $corePath . '/storage/app/public',
    $corePath . '/storage/framework',
    $corePath . '/storage/framework/cache',
    $corePath . '/storage/framework/sessions',
    $corePath . '/storage/framework/views',
    $corePath . '/storage/logs',
    $corePath . '/bootstrap/cache',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0775, true)) {
            echo '<span class="ok">&#10003; Created: ' . htmlspecialchars(str_replace($webroot, '', $dir)) . '</span><br>';
        } else {
            echo '<span class="fail">&#10007; Failed to create: ' . htmlspecialchars(str_replace($webroot, '', $dir)) . '</span><br>';
        }
    } else {
        $writable = is_writable($dir);
        $icon = $writable ? '<span class="ok">&#10003;</span>' : '<span class="warn">&#9888;</span>';
        echo $icon . ' ' . htmlspecialchars(str_replace($webroot, '', $dir))
            . ($writable ? '' : ' <span class="warn">(not writable — fixing...)</span>') . '<br>';
        if (!$writable) {
            chmod($dir, 0775);
        }
    }
}
echo '</div>';

// ──────────────────────────────────────────────
// STEP 2: Set permissions on storage
// ──────────────────────────────────────────────
echo '<h2>Step 2: Set Permissions</h2><div class="box">';
$permDirs = [
    $corePath . '/storage',
    $corePath . '/bootstrap/cache',
];
foreach ($permDirs as $dir) {
    if (is_dir($dir)) {
        // Recursively chmod
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        $count = 0;
        foreach ($iterator as $item) {
            chmod($item->getPathname(), $item->isDir() ? 0775 : 0664);
            $count++;
        }
        chmod($dir, 0775);
        echo '<span class="ok">&#10003; Set permissions on ' . htmlspecialchars(str_replace($webroot, '', $dir))
            . ' (' . $count . ' items)</span><br>';
    }
}
echo '</div>';

// ──────────────────────────────────────────────
// STEP 3: Create storage symlink
// ──────────────────────────────────────────────
echo '<h2>Step 3: Storage Symlink (webroot/storage → core/storage/app/public)</h2><div class="box">';

if (is_link($storageLnk)) {
    $target = readlink($storageLnk);
    if ($target === $storageSrc) {
        echo '<span class="ok">&#10003; Symlink already correct: storage → ' . htmlspecialchars($storageSrc) . '</span>';
    } else {
        echo '<span class="warn">&#9888; Symlink exists but points to wrong target: ' . htmlspecialchars($target) . '</span><br>';
        unlink($storageLnk);
        if (symlink($storageSrc, $storageLnk)) {
            echo '<span class="ok">&#10003; Recreated symlink correctly.</span>';
        } else {
            echo '<span class="fail">&#10007; Failed to recreate symlink.</span>';
        }
    }
} elseif (is_dir($storageLnk)) {
    echo '<span class="warn">&#9888; A real directory named "storage" exists at webroot. Removing it...</span><br>';
    // Remove the directory recursively
    $removed = removeDir($storageLnk);
    if ($removed) {
        echo '<span class="ok">&#10003; Removed old storage directory.</span><br>';
        if (symlink($storageSrc, $storageLnk)) {
            echo '<span class="ok">&#10003; Symlink created: webroot/storage → core/storage/app/public</span>';
        } else {
            echo '<span class="fail">&#10007; Could not create symlink (host may not allow symlinks).</span><br>';
            goto fallback;
        }
    } else {
        echo '<span class="fail">&#10007; Could not remove directory. Try manually in File Manager, then re-run.</span>';
    }
} elseif (file_exists($storageLnk)) {
    echo '<span class="warn">&#9888; A file named "storage" exists at webroot. Removing...</span><br>';
    unlink($storageLnk);
    if (symlink($storageSrc, $storageLnk)) {
        echo '<span class="ok">&#10003; Symlink created.</span>';
    } else {
        goto fallback;
    }
} else {
    // Nothing there — create symlink
    if (symlink($storageSrc, $storageLnk)) {
        echo '<span class="ok">&#10003; Symlink created: webroot/storage → core/storage/app/public</span>';
    } else {
        echo '<span class="fail">&#10007; Could not create symlink (host may not support symlinks).</span><br>';
        goto fallback;
    }
}

goto done;

fallback:
echo '<br><span class="warn">&#9888; Symlinks not supported — using filesystem copy approach instead.</span><br>';
echo '<span class="warn">Uploaded files will work but may need a different URL for public assets.</span>';
// Create a real directory as fallback
if (!is_dir($storageLnk)) {
    mkdir($storageLnk, 0775, true);
    echo '<br><span class="ok">&#10003; Created webroot/storage as real directory (fallback).</span>';
}

done:
echo '</div>';

// ──────────────────────────────────────────────
// STEP 4: Write .gitignore placeholders
// ──────────────────────────────────────────────
echo '<h2>Step 4: Framework Placeholder Files</h2><div class="box">';
$placeholders = [
    $corePath . '/storage/framework/cache/.gitignore'    => "*\n!.gitignore\n",
    $corePath . '/storage/framework/sessions/.gitignore' => "*\n!.gitignore\n",
    $corePath . '/storage/framework/views/.gitignore'    => "*\n!.gitignore\n",
    $corePath . '/storage/logs/.gitignore'               => "*\n!.gitignore\n",
];
foreach ($placeholders as $file => $content) {
    if (!file_exists($file)) {
        file_put_contents($file, $content);
        echo '<span class="ok">&#10003; Created: ' . htmlspecialchars(str_replace($webroot, '', $file)) . '</span><br>';
    } else {
        echo '<span class="ok">&#10003; Exists: ' . htmlspecialchars(str_replace($webroot, '', $file)) . '</span><br>';
    }
}
echo '</div>';

// ──────────────────────────────────────────────
// STEP 5: Summary
// ──────────────────────────────────────────────
echo '<h2>Step 5: Summary</h2><div class="box">';
$checks = [
    'core/storage is writable'          => is_writable($corePath . '/storage'),
    'core/bootstrap/cache is writable'  => is_writable($corePath . '/bootstrap/cache'),
    'core/storage/logs is writable'     => is_writable($corePath . '/storage/logs'),
    'core/storage/framework is writable'=> is_writable($corePath . '/storage/framework'),
    'webroot/storage exists'            => file_exists($storageLnk),
    'core/vendor/autoload.php exists'   => file_exists($corePath . '/vendor/autoload.php'),
    'core/.env exists'                  => file_exists($corePath . '/.env'),
    'webroot/.env exists'               => file_exists($webroot . '/.env'),
];
$allGood = true;
foreach ($checks as $label => $result) {
    if ($result) {
        echo '<span class="ok">&#10003; ' . htmlspecialchars($label) . '</span><br>';
    } else {
        echo '<span class="fail">&#10007; ' . htmlspecialchars($label) . '</span><br>';
        $allGood = false;
    }
}
echo '<br>';
if ($allGood) {
    echo '<span class="ok" style="font-size:16px;">&#10003; All checks passed! Your site should be working.</span>';
} else {
    echo '<span class="warn" style="font-size:16px;">&#9888; Some checks failed — see above.</span>';
}
echo '</div>';

echo '<p style="color:#ff4444;margin-top:30px;"><strong>&#9888; DELETE this file after use!</strong></p>';
echo '</body></html>';

// Helper to recursively remove a directory
function removeDir(string $dir): bool {
    if (!is_dir($dir)) return false;
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }
    return rmdir($dir);
}
