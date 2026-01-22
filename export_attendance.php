<?php
require_once '../auth/auth_check.php';
require_once '../core/db.php';

$date = $_GET['date'];

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="attendance_'.$date.'.csv"');

$output = fopen("php://output", "w");
fputcsv($output, ['ID Number', 'Name', 'Status']);

$stmt = $conn->prepare(
    "SELECT s.id_number, CONCAT(s.last_name, ', ', s.first_name) AS name, a.status
     FROM attendance a
     JOIN students s ON s.serial_no = a.student_id
     WHERE a.date = ?"
);
$stmt->bind_param("s", $date);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
