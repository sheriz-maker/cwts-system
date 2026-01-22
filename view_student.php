<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../core/db.php';

$serial = (int)($_GET['serial_no'] ?? 0);
$row = $conn->query("SELECT * FROM students WHERE serial_no=$serial AND deleted_at IS NULL")->fetch_assoc();
if (!$row) die("Student not found.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>View Student</h1>
    <table class="student-view">
        <tr><th>ID Number:</th><td><?= htmlspecialchars($row['id_number']) ?></td></tr>
        <tr><th>Last Name:</th><td><?= htmlspecialchars($row['last_name']) ?></td></tr>
        <tr><th>First Name:</th><td><?= htmlspecialchars($row['first_name']) ?></td></tr>
        <tr><th>Classification:</th><td><?= htmlspecialchars($row['classification']) ?></td></tr>
        <tr><th>Campus:</th><td><?= htmlspecialchars($row['campus']) ?></td></tr>
        <tr><th>Course:</th><td><?= htmlspecialchars($row['course']) ?></td></tr>
    </table>
    <br>
    <a href="index.php" class="button">⬅ Back</a>
</div>
<script src="../assets/js/script.js" defer></script>
</body>
</html>