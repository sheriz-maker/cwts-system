<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../core/db.php';

$res = $conn->query(
    "SELECT a.*, s.last_name, s.first_name 
     FROM absence_alerts a 
     JOIN students s ON s.serial_no=a.student_id"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Absence Alerts</title>
    <link rel="stylesheet" href="/cwts-system/assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>Absence Alerts</h1>
    <table>
        <thead>
            <tr><th>Student</th><th>Absences</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php while ($r = $res->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($r['last_name'] . ', ' . $r['first_name']) ?></td>
                <td><?= $r['absences'] ?></td>
                <td><?= $r['acknowledged'] ? '✔' : '⚠' ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <a href="/cwts-system/index.php" class="button logout">⬅ Back to Dashboard</a>
</div>
<script src="/cwts-system/assets/js/script.js" defer></script>
</body>
</html>