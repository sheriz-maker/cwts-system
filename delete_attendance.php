<?php
session_start();
require_once '../auth/auth_check.php';
require_once '../core/db.php';
require_once '../core/audit.php';

// ADMIN ONLY
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

// ===============================
// DELETE SINGLE ATTENDANCE RECORD
// ===============================
if (isset($_GET['attendance_id'])) {

    $attendance_id = (int)$_GET['attendance_id'];

    $stmt = $conn->prepare(
        "DELETE FROM attendance WHERE attendance_id = ?"
    );
    $stmt->bind_param("i", $attendance_id);
    $stmt->execute();

    audit($_SESSION['user_id'], 'DELETE', 'attendance', $attendance_id);

    header("Location: view_attendance.php");
    exit;
}

// ===================================
// DELETE ATTENDANCE BY DATE RANGE
// ===================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $from = $_POST['from'];
    $to   = $_POST['to'];

    if (empty($from) || empty($to)) {
        die("Date range required");
    }

    $stmt = $conn->prepare(
        "DELETE FROM attendance WHERE date BETWEEN ? AND ?"
    );
    $stmt->bind_param("ss", $from, $to);
    $stmt->execute();

    audit($_SESSION['user_id'], 'DELETE_RANGE', 'attendance', 0);

    header("Location: attendance_report.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Attendance</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Delete Attendance Records</h2>

    <form method="post">
        <label>From Date</label>
        <input type="date" name="from" required>

        <label>To Date</label>
        <input type="date" name="to" required>

        <button type="submit" class="danger">
            Delete Attendance Records
        </button>
    </form>

    <br>
    <a href="attendance_report.php" class="button">⬅ Back</a>
</div>

</body>
</html>