<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/audit.php';

if ($_SESSION['role'] !== 'admin') die("Denied");

$serial = (int)$_GET['serial_no'];
$old = $conn->query("SELECT * FROM students WHERE serial_no=$serial")->fetch_assoc();
$conn->query("UPDATE students SET deleted_at=NOW() WHERE serial_no=$serial");
audit($conn,$_SESSION['user_id'],'DELETE','students',$serial,$old,null);

header("Location: /cwts-system/students/index.php");
exit;