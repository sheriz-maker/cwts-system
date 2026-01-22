<?php
require_once '../auth/auth_check.php';
require_once '../core/db.php';

$filter_group = $_GET['group'] ?? 'all'; // Default to 'all'

// Query to fetch students, with NSTP 1 prioritized first, then A.Y., campus, course, and name
$result = $conn->query("
    SELECT * FROM students
    WHERE deleted_at IS NULL
    ORDER BY 
        CASE WHEN nstp_term = 'NSTP 1' THEN 1 ELSE 2 END,  -- NSTP 1 first
        school_year ASC, 
        campus ASC, 
        course ASC, 
        last_name ASC, 
        first_name ASC
");

// Group data into arrays for separation (NSTP > A.Y. > Campus)
$grouped = [];
while ($row = $result->fetch_assoc()) {
    $key = $row['nstp_term'] . '|' . $row['school_year'] . '|' . $row['campus'];
    $grouped[$key][] = $row;
}

// Build dropdown options
$group_options = ['all' => 'All'];
foreach ($grouped as $key => $students) {
    list($nstp, $ay, $campus) = explode('|', $key);
    $label = "NSTP $nstp - $ay - $campus";
    $group_options[$key] = $label;
}

// Filter grouped data if a specific group is selected
if ($filter_group !== 'all') {
    $grouped = isset($grouped[$filter_group]) ? [$filter_group => $grouped[$filter_group]] : [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Students</title>
    <link rel="stylesheet" href="/cwts-system/assets/css/style.css">
    <script>
        function handleAction(select, serialNo) {
            const action = select.value;
            if (action === 'view') {
                window.location.href = 'view_student.php?serial_no=' + serialNo;
            } else if (action === 'edit') {
                window.location.href = 'manage_students.php?id=' + serialNo;
            }
            select.value = ''; // Reset dropdown after action
        }
    </script>
</head>
<body>

<div class="container">
    <h1>Students List</h1>

    <!-- Group Filter Dropdown -->
    <form method="get" style="margin-bottom: 20px;">
        <label for="group">Filter by Group:</label>
        <select name="group" id="group" onchange="this.form.submit()">
            <?php foreach ($group_options as $value => $label): ?>
                <option value="<?= htmlspecialchars($value) ?>" <?= $filter_group === $value ? 'selected' : '' ?>>
                    <?= htmlspecialchars($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if (empty($grouped)): ?>
        <p>No students found<?= $filter_group !== 'all' ? ' for the selected group' : '' ?>.</p>
    <?php else: ?>
        <?php foreach ($grouped as $groupKey => $students): ?>
            <?php list($nstp, $ay, $campus) = explode('|', $groupKey); ?>
            <h2><?= htmlspecialchars($nstp) ?> - A.Y. <?= htmlspecialchars($ay) ?> - <?= htmlspecialchars($campus) ?> Campus</h2>

            <?php
            // Group by course within this section
            $byCourse = [];
            foreach ($students as $student) {
                $byCourse[$student['course']][] = $student;
            }
            ksort($byCourse); // Sort courses alphabetically
            ?>

            <?php foreach ($byCourse as $course => $courseStudents): ?>
                <h3>Course: <?= htmlspecialchars($course) ?></h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID Number</th>
                            <th>Name</th>
                            <th>Status</th>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courseStudents as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['id_number']) ?></td>
                                <td><?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?></td>
                                <td><?= htmlspecialchars($student['academic_status']) ?></td>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <td>
                                        <select onchange="handleAction(this, <?= $student['serial_no'] ?>)">
                                            <option value="">Select Action</option>
                                            <option value="view">View Student</option>
                                            <option value="edit">Edit</option>
                                        </select>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <br>
    <a href="../index.php" class="button">⬅ Dashboard</a>
</div>

</body>
</html>