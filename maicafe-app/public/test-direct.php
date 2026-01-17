<?php
// Direct test file - access at: http://localhost/maicafe-app/public/test-direct.php
// This tests if PHP and the public folder are accessible

echo "<h1>Direct Access Test</h1>";
echo "<p style='color: green;'>✅ PHP is working!</p>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Laravel index.php exists: " . (file_exists(__DIR__ . '/index.php') ? 'YES ✅' : 'NO ❌') . "</p>";

echo "<hr>";
echo "<h2>Test Laravel Routing</h2>";
echo "<p>If you can see this file, the public folder is accessible.</p>";
echo "<p>Now try accessing Laravel:</p>";
echo "<ul>";
echo "<li><a href='index.php'>index.php (should show Laravel app)</a></li>";
echo "<li><a href='/maicafe-app/public/'>http://localhost/maicafe-app/public/</a></li>";
echo "<li><a href='/maicafe-app/public/admin/dashboard'>http://localhost/maicafe-app/public/admin/dashboard</a></li>";
echo "</ul>";

echo "<hr>";
echo "<h2>If admin/dashboard gives 404:</h2>";
echo "<ol>";
echo "<li>Check if mod_rewrite is enabled: WAMP menu → Apache → Apache modules → 'rewrite_module' should be checked</li>";
echo "<li>Check AllowOverride: In httpd.conf, find &lt;Directory \"C:/wamp64/www\"&gt; and set AllowOverride All</li>";
echo "<li>Restart Apache after making changes</li>";
echo "</ol>";

