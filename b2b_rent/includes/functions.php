<?php
function escape($data) {
    return htmlspecialchars(strip_tags($data));
}

function isProfileCompleted($user_id, $pdo) {
    $stmt = $pdo->prepare("SELECT is_profile_completed FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return (bool)$stmt->fetchColumn();
}
?>