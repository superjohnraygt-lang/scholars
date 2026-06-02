<?php
// Generate hashed password for 'admin_pass123'
$password = 'admin_pass123';
$hashed = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash Generator</h2>";
echo "<p>Your password: <strong>admin_pass123</strong></p>";
echo "<p>Hashed password: <strong>" . $hashed . "</strong></p>";
echo "<hr>";
echo "<h3>Copy this SQL command and run it in phpMyAdmin:</h3>";
echo "<textarea style='width:100%; height:100px; font-family:monospace;'>" . htmlspecialchars("INSERT INTO users (username, email, password, role) VALUES ('admin1', 'admin1@email.com', '" . $hashed . "', 'admin');") . "</textarea>";
echo "<hr>";
echo "<h3>To test login, use:</h3>";
echo "<p><strong>Username:</strong> admin1</p>";
echo "<p><strong>Password:</strong> admin_pass123</p>";
?>