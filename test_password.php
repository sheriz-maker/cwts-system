<?php
// Test admin password hash
$hash = '$2y$10$wHcUBvXYz6b0GkYN9B5Z2Op8OJ6FhxqKMUfp1dHDD7d1UFVb1qG3W';
$input_password = 'admin123';

if (password_verify($input_password, $hash)) {
    echo "✅ Password works!";
} else {
    echo "❌ Password does NOT match!";
}
