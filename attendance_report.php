<?php
require_once '../auth/auth_check.php';
require_once '../core/db.php';

// Fetch attendance reports grouped by date
$sql = "
    SELECT 
        a.date,
        COUNT(a.attendance_id) AS total,
        SUM(a.status = 'Absent') AS absents
    FROM attendance a
    WHERE a.deleted_at IS NULL
    GROUP BY a.date
    ORDER BY a.date DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">
    <h1>Attendance Reports</h1>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Total Students</th>
                <th>Absences</th>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= $row['total'] ?></td>
                        <td><?= $row['absents'] ?></td>

                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <td>
                                <a class="button delete"
                                   href="delete_attendance.php?date=<?= $row['date'] ?>"
                                   onclick="return confirm('Delete attendance for this date?')">
                                   Delete
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No attendance records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="../index.php" class="button">⬅ Back to Dashboard</a>
</div>

</body>
</html>
