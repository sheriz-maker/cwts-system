<?php
require_once '../auth/auth_check.php';
require_once '../core/db.php';

$date = $_GET['date'] ?? date('Y-m-d');

$stmt = $conn->prepare(
    "SELECT s.id_number, s.last_name, s.first_name, s.course, a.status
     FROM attendance a
     JOIN students s ON s.serial_no = a.student_id
     WHERE a.date = ? AND a.deleted_at IS NULL
     ORDER BY s.course ASC, s.last_name ASC, s.first_name ASC"
);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

// Group by course
$grouped = [];
while ($row = $result->fetch_assoc()) {
    $grouped[$row['course']][] = $row;
}
ksort($grouped); // Sort courses alphabetically
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Attendance</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">
    <h1>Attendance for <?= htmlspecialchars($date) ?></h1>

    <form method="get">
        <label>Select Date:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">
        <button type="submit">View</button>
    </form>

    <br>

    <?php if (empty($grouped)): ?>
        <p>No attendance records found for this date.</p>
    <?php else: ?>
        <?php foreach ($grouped as $course => $students): ?>
            <h3>Course: <?= htmlspecialchars($course) ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>ID Number</th>
                        <th>Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['id_number']) ?></td>
                            <td><?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?></td>
                            <td><?= htmlspecialchars($student['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br>
        <?php endforeach; ?>
    <?php endif; ?>

    <br>
    <a href="../index.php" class="button">⬅ Back to Dashboard</a>
</div>

</body>
</html>