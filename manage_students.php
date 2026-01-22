<?php
require_once '../auth/auth_check.php';
require_once '../core/db.php';

if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$id = $_GET['id'] ?? null;
$editing = false;

if ($id) {
    $editing = true;
    $stmt = $conn->prepare("SELECT * FROM students WHERE serial_no=? AND deleted_at IS NULL");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();

    if (!$student) die("Student not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_number = $_POST['id_number'];
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $course = $_POST['course'];
    $campus = $_POST['campus'];
    $academic_status = $_POST['academic_status'];
    $nstp_term = $_POST['nstp_term'];
    $school_year = $_POST['school_year'];

    if ($editing) {
        $stmt = $conn->prepare("
            UPDATE students SET
            id_number=?, last_name=?, first_name=?, course=?, campus=?,
            academic_status=?, nstp_term=?, school_year=?
            WHERE serial_no=?
        ");
        $stmt->bind_param(
            "ssssssssi",
            $id_number, $last_name, $first_name, $course, $campus,
            $academic_status, $nstp_term, $school_year, $id
        );
    } else {
        $stmt = $conn->prepare("
            INSERT INTO students
            (id_number, last_name, first_name, course, campus, academic_status, nstp_term, school_year)
            VALUES (?,?,?,?,?,?,?,?)
        ");
        $stmt->bind_param(
            "ssssssss",
            $id_number, $last_name, $first_name, $course,
            $campus, $academic_status, $nstp_term, $school_year
        );
    }

    $stmt->execute();
    header("Location: index.php");
    exit;
}

if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("UPDATE students SET deleted_at=NOW() WHERE serial_no=?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $editing ? 'Edit Student' : 'Add Student' ?></title>
    <link rel="stylesheet" href="/cwts-system/assets/css/style.css">
</head>
<body>

<div class="container">
    <h2><?= $editing ? 'Edit Student' : 'Add Student' ?></h2>

    <form method="post">
        <label>ID Number</label>
        <input required name="id_number" placeholder="ID Number" value="<?= htmlspecialchars($student['id_number'] ?? '') ?>">

        <label>Last Name</label>
        <input required name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($student['last_name'] ?? '') ?>">

        <label>First Name</label>
        <input required name="first_name" placeholder="First Name" value="<?= htmlspecialchars($student['first_name'] ?? '') ?>">

        <label>Course</label>
        <input required name="course" placeholder="Course" value="<?= htmlspecialchars($student['course'] ?? '') ?>">

        <label>Campus</label>
        <select name="campus" required>
            <option value="PU Urdaneta" <?= (($student['campus'] ?? '') == 'PU Urdaneta') ? 'selected' : '' ?>>PU Urdaneta</option>
            <option value="PU Tayug" <?= (($student['campus'] ?? '') == 'PU Tayug') ? 'selected' : '' ?>>PU Tayug</option>
        </select>

        <label>Academic Status</label>
        <select name="academic_status">
            <?php foreach (['Active', 'Inactive', 'Completed', 'Dropped'] as $s): ?>
                <option value="<?= $s ?>" <?= (($student['academic_status'] ?? '') == $s) ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>

        <label>NSTP Term</label>
        <select name="nstp_term" required>
            <option value="NSTP 1" <?= (($student['nstp_term'] ?? '') == 'NSTP 1') ? 'selected' : '' ?>>NSTP 1</option>
            <option value="NSTP 2" <?= (($student['nstp_term'] ?? '') == 'NSTP 2') ? 'selected' : '' ?>>NSTP 2</option>
        </select>

        <label>School Year</label>
        <select name="school_year" required>
            <option value="2024-2025" <?= (($student['school_year'] ?? '') == '2024-2025') ? 'selected' : '' ?>>2024-2025</option>
            <option value="2025-2026" <?= (($student['school_year'] ?? '') == '2025-2026') ? 'selected' : '' ?>>2025-2026</option>
        </select>

        <button class="button"><?= $editing ? 'Update' : 'Add' ?></button>
    </form>

    <?php if ($editing): ?>
        <a class="button delete" href="?delete=<?= $id ?>" onclick="return confirm('Delete this student?')">Delete</a>
    <?php endif; ?>

    <br>
    <a href="index.php" class="button">⬅ Back to List</a>
    <a href="../index.php" class="button">⬅ Dashboard</a>
</div>

</body>
</html>