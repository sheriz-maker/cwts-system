<?php
require_once '../auth/auth_check.php';
require_once '../core/db.php';
require_once '../core/audit.php';

// Only admins can access
if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

// Handle Add or Edit submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serial = $_POST['serial_no'] ?? null;

    if ($serial) { // Editing existing student
        $serial = (int)$serial;
        $old = $conn->query("SELECT * FROM students WHERE serial_no=$serial")->fetch_assoc();

        $stmt = $conn->prepare(
            "UPDATE students SET 
            id_number=?, last_name=?, first_name=?, middle_name=?, classification=?, campus=?, course=?
            WHERE serial_no=?"
        );
        $stmt->bind_param(
            "sssssssi",
            $_POST['id_number'],
            $_POST['last_name'],
            $_POST['first_name'],
            $_POST['middle_name'],
            $_POST['classification'],
            $_POST['campus'],
            $_POST['course'],
            $serial
        );
        $stmt->execute();
        audit($conn, $_SESSION['user_id'], 'UPDATE', 'students', $serial, $old, $_POST);

    } else { // Adding new student
        $stmt = $conn->prepare(
            "INSERT INTO students 
            (id_number,last_name,first_name,middle_name,classification,campus,course) 
            VALUES (?,?,?,?,?,?,?)"
        );
        $stmt->bind_param(
            "sssssss",
            $_POST['id_number'],
            $_POST['last_name'],
            $_POST['first_name'],
            $_POST['middle_name'],
            $_POST['classification'],
            $_POST['campus'],
            $_POST['course']
        );
        $stmt->execute();
        audit($conn, $_SESSION['user_id'], 'CREATE', 'students', null, null, $_POST);
    }

    header("Location: manage_students.php?success=1");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $serial = (int)$_GET['delete'];
    $old = $conn->query("SELECT * FROM students WHERE serial_no=$serial")->fetch_assoc();
    $conn->query("UPDATE students SET deleted_at=NOW() WHERE serial_no=$serial");
    audit($conn, $_SESSION['user_id'], 'DELETE', 'students', $serial, $old, null);
    header("Location: manage_students.php?deleted=1");
    exit;
}

// Fetch student for edit if serial_no is provided
$edit_serial = $_GET['edit'] ?? null;
$row = $edit_serial ? $conn->query("SELECT * FROM students WHERE serial_no=".(int)$edit_serial)->fetch_assoc() : null;

// Fetch all students
$students = $conn->query("SELECT * FROM students WHERE deleted_at IS NULL ORDER BY last_name");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js" defer></script>
</head>
<body>
<div class="container">
    <h1>Manage Students</h1>
    <a href="../index.php" class="button">Back to Dashboard</a>

    <h2><?= $row ? "Edit Student" : "Add New Student" ?></h2>
    <form method="post">
        <?php if($row): ?>
            <input type="hidden" name="serial_no" value="<?= $row['serial_no'] ?>">
        <?php endif; ?>

        <label>ID Number</label>
        <input type="text" name="id_number" value="<?= htmlspecialchars($row['id_number'] ?? '') ?>" required>

        <label>Last Name</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($row['last_name'] ?? '') ?>" required>

        <label>First Name</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($row['first_name'] ?? '') ?>" required>

        <label>Middle Name</label>
        <input type="text" name="middle_name" value="<?= htmlspecialchars($row['middle_name'] ?? '') ?>">

        <label>Classification</label>
        <select name="classification">
            <?php
            $classes = ['Regular','Irregular','Failed','Dropped'];
            foreach($classes as $class) {
                $selected = ($row['classification'] ?? '') === $class ? 'selected' : '';
                echo "<option value='$class' $selected>$class</option>";
            }
            ?>
        </select>

        <label>Campus</label>
        <select name="campus" required>
            <option value="PU Urdaneta" <?= ($row['campus'] ?? '') === 'PU Urdaneta' ? 'selected' : '' ?>>PU Urdaneta</option>
            <option value="PU Tayug" <?= ($row['campus'] ?? '') === 'PU Tayug' ? 'selected' : '' ?>>PU Tayug</option>
        </select>

        <label>Course</label>
        <input type="text" name="course" value="<?= htmlspecialchars($row['course'] ?? '') ?>" required>

        <button type="submit"><?= $row ? "Save Changes" : "Add Student" ?></button>

        <?php if($row): ?>
            <a href="manage_students.php?delete=<?= $row['serial_no'] ?>" class="button delete" onclick="return confirm('Delete this student?')">Delete Student</a>
        <?php endif; ?>
    </form>

    <h2>All Students</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Campus</th>
            <th>Actions</th>
        </tr>
        <?php while ($s = $students->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($s['id_number']) ?></td>
                <td><?= htmlspecialchars($s['last_name'] . ', ' . $s['first_name']) ?></td>
                <td><?= htmlspecialchars($s['campus']) ?></td>
                <td>
                    <a href="manage_students.php?edit=<?= $s['serial_no'] ?>" class="button">Edit</a>
                    <a href="manage_students.php?delete=<?= $s['serial_no'] ?>" class="button delete" onclick="return confirm('Delete this student?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>