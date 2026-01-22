<?php
require_once '../auth/auth_check.php';
require_once '../core/db.php';
require_once '../core/absence_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['attendance'] as $sid => $status) {
        $stmt = $conn->prepare("INSERT INTO attendance 
            (student_id,date,status,recorded_by) 
            VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status)");
        $stmt->bind_param("issi", $sid, $_POST['date'], $status, $_SESSION['user_id']);
        $stmt->execute();
    }
    runAbsenceCheck($conn);
    header("Location: take_attendance.php?date=" . $_POST['date']);
    exit;
}

$students = $conn->query("SELECT serial_no, last_name, first_name, course FROM students WHERE deleted_at IS NULL ORDER BY course ASC, last_name ASC, first_name ASC");
$date = $_GET['date'] ?? date('Y-m-d');

// Group by course
$grouped = [];
while ($s = $students->fetch_assoc()) {
    $grouped[$s['course']][] = $s;
}
ksort($grouped); // Sort courses alphabetically
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Attendance</title>
    <link rel="stylesheet" href="/cwts-system/assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>Take Attendance</h1>
    <form method="get">
        <label>Select Date: <input type="date" name="date" value="<?= htmlspecialchars($date) ?>"></label>
        <button type="submit">Go</button>
    </form>

    <form method="post">
        <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
        
        <?php if (empty($grouped)): ?>
            <p>No students found.</p>
        <?php else: ?>
            <?php foreach ($grouped as $course => $courseStudents): ?>
                <h3>Course: <?= htmlspecialchars($course) ?></h3>
                <table>
                    <thead>
                        <tr><th>Student</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courseStudents as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['last_name'] . ', ' . $s['first_name']) ?></td>
                                <td>
                                    <select name="attendance[<?= $s['serial_no'] ?>]">
                                        <option>Present</option>
                                        <option>Absent</option>
                                        <option>Excused</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="actions">
            <button type="submit">Save Attendance</button>
            <a href="/cwts-system/index.php" class="button logout">Back</a>
        </div>
    </form>
</div>
<script src="/cwts-system/assets/js/script.js" defer></script>
</body>
</html>