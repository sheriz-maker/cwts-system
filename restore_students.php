<?php
require_once '../auth/auth_check.php';
require_once '../core/db.php';
require_once '../core/audit.php';

// ADMIN ONLY
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

// HANDLE RESTORE
if (isset($_POST['restore'])) {
    $serial_no = (int)$_POST['serial_no'];

    $stmt = $conn->prepare(
        "UPDATE students SET deleted_at = NULL WHERE serial_no = ?"
    );
    $stmt->bind_param("i", $serial_no);
    $stmt->execute();

    audit($_SESSION['user_id'], 'RESTORE', 'students', $serial_no);

    header("Location: restore_students.php");
    exit;
}

// FETCH DELETED STUDENTS
$result = $conn->query(
    "SELECT serial_no, id_number, last_name, first_name, course, campus, deleted_at
     FROM students
     WHERE deleted_at IS NOT NULL
     ORDER BY deleted_at DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restore Students</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">
    <h1>Restore Deleted Students</h1>

    <?php if ($result->num_rows === 0): ?>
        <p>No deleted students found.</p>
    <?php else: ?>

    <table>
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Name</th>
                <th>Course</th>
                <th>Campus</th>
                <th>Deleted On</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id_number']) ?></td>
                <td><?= htmlspecialchars($row['last_name'] . ', ' . $row['first_name']) ?></td>
                <td><?= htmlspecialchars($row['course']) ?></td>
                <td><?= htmlspecialchars($row['campus']) ?></td>
                <td><?= htmlspecialchars($row['deleted_at']) ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="serial_no"
                               value="<?= $row['serial_no'] ?>">
                        <button type="submit" name="restore">
                            Restore
                        </button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>

        </tbody>
    </table>

    <?php endif; ?>

    <br>
    <a href="index.php" class="button">⬅ Back to Students</a>
    <a href="../index.php" class="button">⬅ Dashboard</a>
</div>

</body>
</html>
