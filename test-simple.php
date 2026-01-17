<?php
// Simple test - access at: http://localhost/maicafe-app/test-simple.php
echo "<h1>PHP is Working!</h1>";
echo "<p>If you can see this, PHP and Apache are working.</p>";
echo "<p>Current file location: " . __FILE__ . "</p>";
echo "<p>Laravel app location: " . __DIR__ . "\\maicafe-app\\public\\index.php</p>";
echo "<p>Laravel index.php exists: " . (file_exists(__DIR__ . '/maicafe-app/public/index.php') ? 'YES ✅' : 'NO ❌') . "</p>";

echo "<hr>";
echo "<h2>Correct URLs to Access:</h2>";
echo "<ul>";
echo "<li><a href='/maicafe-app/maicafe-app/public/'>http://localhost/maicafe-app/maicafe-app/public/</a></li>";
echo "<li><a href='/maicafe-app/maicafe-app/public/admin/dashboard'>http://localhost/maicafe-app/maicafe-app/public/admin/dashboard</a></li>";
echo "</ul>";

echo "<hr>";
echo "<h2>If the above URLs don't work:</h2>";
echo "<ol>";
echo "<li><strong>Enable mod_rewrite:</strong> WAMP menu → Apache → Apache modules → Check 'rewrite_module'</li>";
echo "<li><strong>Set AllowOverride All:</strong> Open httpd.conf, find &lt;Directory \"C:/wamp64/www\"&gt; and change AllowOverride None to AllowOverride All</li>";
echo "<li><strong>Restart Apache:</strong> WAMP menu → Restart All Services</li>";
echo "</ol>";

