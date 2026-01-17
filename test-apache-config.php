<?php
// Test Apache configuration
// Access this file at: http://localhost/maicafe-app/test-apache-config.php

echo "<h1>Apache Configuration Test</h1>";

// Test 1: Check if mod_rewrite is loaded
echo "<h2>1. mod_rewrite Module</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p style='color: green;'>✅ mod_rewrite is ENABLED</p>";
    } else {
        echo "<p style='color: red;'>❌ mod_rewrite is NOT ENABLED</p>";
        echo "<p><strong>Fix:</strong> WAMP menu → Apache → Apache modules → Enable 'rewrite_module'</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Cannot check modules (function not available)</p>";
    echo "<p>Check manually: WAMP menu → Apache → Apache modules → Look for 'rewrite_module'</p>";
}

// Test 2: Check .htaccess file
echo "<h2>2. .htaccess Files</h2>";
$rootHtaccess = __DIR__ . '/.htaccess';
$publicHtaccess = __DIR__ . '/maicafe-app/public/.htaccess';

if (file_exists($rootHtaccess)) {
    echo "<p style='color: green;'>✅ Root .htaccess exists: " . basename($rootHtaccess) . "</p>";
} else {
    echo "<p style='color: red;'>❌ Root .htaccess NOT FOUND</p>";
}

if (file_exists($publicHtaccess)) {
    echo "<p style='color: green;'>✅ Public .htaccess exists: maicafe-app/public/.htaccess</p>";
} else {
    echo "<p style='color: red;'>❌ Public .htaccess NOT FOUND</p>";
}

// Test 3: Check PHP version
echo "<h2>3. PHP Version</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test 4: Check if public/index.php exists
echo "<h2>4. Laravel Files</h2>";
$indexFile = __DIR__ . '/maicafe-app/public/index.php';
if (file_exists($indexFile)) {
    echo "<p style='color: green;'>✅ Laravel index.php exists</p>";
} else {
    echo "<p style='color: red;'>❌ Laravel index.php NOT FOUND</p>";
}

// Test 5: Check .env file
echo "<h2>5. Environment Configuration</h2>";
$envFile = __DIR__ . '/maicafe-app/.env';
if (file_exists($envFile)) {
    echo "<p style='color: green;'>✅ .env file exists</p>";
} else {
    echo "<p style='color: red;'>❌ .env file NOT FOUND</p>";
}

// Test 6: Recommended URLs
echo "<h2>6. How to Access Your Application</h2>";
echo "<p><strong>Try these URLs:</strong></p>";
echo "<ul>";
echo "<li><a href='/maicafe-app/public/'>http://localhost/maicafe-app/public/</a></li>";
echo "<li><a href='/maicafe-app/public/admin/dashboard'>http://localhost/maicafe-app/public/admin/dashboard</a></li>";
echo "</ul>";

echo "<hr>";
echo "<h2>Next Steps if Still Getting 404:</h2>";
echo "<ol>";
echo "<li>Enable mod_rewrite: WAMP menu → Apache → Apache modules → Check 'rewrite_module'</li>";
echo "<li>Set AllowOverride All in httpd.conf: Find &lt;Directory \"C:/wamp64/www\"&gt; and change AllowOverride None to AllowOverride All</li>";
echo "<li>Restart Apache: WAMP menu → Restart All Services</li>";
echo "<li>Try accessing: <a href='/maicafe-app/public/'>http://localhost/maicafe-app/public/</a></li>";
echo "</ol>";

