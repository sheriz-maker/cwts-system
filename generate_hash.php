<?php
// Set your new password here
$password = 'admin123'; // You can change this to any password you want

// Generate bcrypt hash
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Your new hash is: <br><br>";
echo $hash;
