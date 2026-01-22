<?php
require_once '../auth/auth_check.php';
require_once '../core/db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=attendance.xls");

$res = $conn->query("SELECT a.date, s.last_name, s.first_name, a.status
                     FROM attendance a
                     JOIN students s ON s.serial_no = a.student_id
                     WHERE a.deleted_at IS NULL
                     ORDER BY a.date DESC");

echo "Date\tLast Name\tFirst Name\tStatus\n";

while ($row = $res->fetch_assoc()) {
    echo $row['date'] . "\t" . $row['last_name'] . "\t" . $row['first_name'] . "\t" . $row['status'] . "\n";
}
