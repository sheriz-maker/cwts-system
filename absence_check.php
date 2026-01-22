<?php
function runAbsenceCheck($conn) {
    // Get students with 3+ absences in the last 30 days
    $stmt = $conn->prepare(
        "SELECT student_id, COUNT(*) as absences
         FROM attendance
         WHERE status='Absent' AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
         GROUP BY student_id
         HAVING absences >= 3"
    );
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        // Insert or update absence_alerts
        $conn->prepare(
            "INSERT INTO absence_alerts (student_id, absences, acknowledged)
             VALUES (?, ?, 0)
             ON DUPLICATE KEY UPDATE absences=VALUES(absences)"
        )->execute([$row['student_id'], $row['absences']]);
    }
}