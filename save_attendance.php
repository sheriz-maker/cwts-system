<?php
require_once '../auth/auth_check.php';
require_once '../core/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $conn->real_escape_string($_POST['date']);
    $statuses = $_POST['status'];

    foreach ($statuses as $student_id => $status) {
        $student_id = (int)$student_id;
        $status = $conn->real_escape_string($status);

        $check = $conn->query("SELECT * FROM attendance WHERE student_id=$student_id AND date='$date' AND deleted_at IS NULL");

        if ($check->num_rows > 0) {
            $conn->query("UPDATE attendance SET status='$status' WHERE student_id=$student_id AND date='$date'");
        } else {
            $conn->query("INSERT INTO attendance (student_id, date, status) VALUES ($student_id, '$date', '$status')");
        }
    }

    header("Location: attendance_report.php");
    exit;
} else {
    header("Location: take_attendance.php");
    exit;
}
