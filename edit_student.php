<?php
require_once '../auth/auth_check.php';
require_once '../core/db.php';
require_once '../core/audit.php';

// ADMIN ONLY
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

// VALIDATE serial_no
if (!isset($_GET['serial_no']) || !is_numeric($_GET['serial_no'])) {
    die("Student not found.");
}

$serial_no = (int)$_GET['serial_no'];

// FETCH STUDENT
$stmt = $conn->prepare(
    "SELECT * FROM students WHERE serial_no = ? AND deleted_at IS NULL"
);
$stmt->bind_param("i", $serial_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Student not found.");
}

$student = $result->fetch_assoc();

// ================= UPDATE =================
if (isset($_POST['update'])) {

    $id_number  = $_POST['id_number'];
    $last_name  = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $course     = $_POST['course'];
    $campus     = $_POST['campus'];

    $stmt = $conn->prepare("
        UPDATE students
        SET id_number=?, last_name=?, first_name=?, course=?, campus=?
        WHERE serial_no=?
    ");
    $stmt->bind_param(
        "sssssi",
        $id_number, $last_name, $first_name, $course, $campus, $serial_no
    );
    $stmt->execute();

    audit($_SESSION['user_id'], 'UPDATE', 'students', $serial_no);

    header("Location: index.php");
    exit;
}

// ================= DELETE =================
if (isset($_POST['delete'])) {

    $stmt = $conn->prepare(
        "UPDATE students SET deleted_at = NOW() WHERE serial_no = ?"
    );
    $stmt->bind_param("i", $serial_no);
    $stmt->execute();

    audit($_SESSION['user_id'], 'DELETE', 'students', $serial_no);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">
    <h1>Edit Student</h1>

    <form method="post">
        <label>ID Number</label>
        <input type="text" name="id_number"
               value="<?= htmlspecialchars($student['id_number']) ?>" required>

        <label>Last Name</label>
        <input type="text" name="last_name"
               value="<?= htmlspecialchars($student['last_name']) ?>" required>

        <label>First Name</label>
        <input type="text" name="first_name"
               value="<?= htmlspecialchars($student['first_name']) ?>" required>

        <label>Course</label>
        <input type="text" name="course"
               value="<?= htmlspecialchars($student['course']) ?>" required>

        <label>Campus</label>
        <select name="campus" required>
            <option value="PU Urdaneta" <?= $student['campus']=='PU Urdaneta'?'selected':'' ?>>
                PU Urdaneta
            </option>
            <option value="PU Tayug" <?= $student['campus']=='PU Tayug'?'selected':'' ?>>
                PU Tayug
            </option>
        </select>

        <br><br>

        <button type="submit" name="update">Save Changes</button>

        <button type="submit" name="delete"
                class="danger"
                onclick="return confirm('Delete this student?')">
            Delete Student
        </button>
    </form>

    <br>
    <a href="index.php" class="button">⬅ Back</a>
</div>

</body>
</html>