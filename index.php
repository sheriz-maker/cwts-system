<?php
require_once __DIR__ . '/auth/auth_check.php';
require_once __DIR__ . '/core/db.php';

// Fetch all students
$res = $conn->query("SELECT * FROM students WHERE deleted_at IS NULL ORDER BY last_name");
?>
<link rel="stylesheet" href="/cwts-system/assets/css/style.css">
<script src="/cwts-system/assets/js/script.js" defer></script>

<div class="container">
    <h1>CWTS Management System</h1>

    <p>Logged in as <strong><?= htmlspecialchars($_SESSION['role']) ?></strong></p>

    <h3>Student Management</h3>
    <a href="students/index.php" class="button">View Students</a>
    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="students/manage_students.php" class="button">Add Students</a>
    <?php endif; ?>
    <?php if ($_SESSION['role'] === 'admin'): ?>
    <a href="students/restore_students.php" class="button">
        Restore Deleted Students
    </a>
    <?php endif; ?>

    <h3>Attendance</h3>
    <a href="attendance/take_attendance.php" class="button">Take Attendance</a>
    <a href="attendance/view_attendance.php" class="button">View Attendance</a>
    <a href="attendance/attendance_report.php" class="button">Attendance Report</a>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <h3>Admin Tools</h3>
        <a href="attendance/absence_alerts.php" class="button">Absence Alerts</a>
        <a href="attendance/export_attendance_excel.php?from=2025-01-01&to=2025-12-31" class="button">Export Attendance</a>
    <?php endif; ?>

    <br><br>
    <a href="auth/logout.php" class="button logout">Logout</a>
</div>
