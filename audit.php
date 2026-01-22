<?php
function audit($conn, $user_id, $action, $entity, $entity_id, $old = null, $new = null) {
    $stmt = $conn->prepare(
        "INSERT INTO audit_logs (user_id, action, entity, entity_id, old_data, new_data) 
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "ississ",
        $user_id,
        $action,
        $entity,
        $entity_id,
        json_encode($old),
        json_encode($new)
    );
    $stmt->execute();
}