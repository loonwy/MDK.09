<?php
session_start();
header('Content-Type: application/json');

require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Не авторизован']);
    exit;
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if (!$post_id || !in_array($action, ['like', 'unlike'])) {
    echo json_encode(['success' => false, 'error' => 'Неверные данные']);
    exit;
}

try {
    if ($action === 'like') {
        $stmt = $pdo->prepare("INSERT IGNORE INTO likes (user_id, post_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $post_id]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$_SESSION['user_id'], $post_id]);
    }
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $like_count = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$_SESSION['user_id'], $post_id]);
    $user_liked = $stmt->fetch() ? true : false;
    
    echo json_encode([
        'success' => true,
        'likes' => $like_count,
        'user_liked' => $user_liked
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Ошибка базы данных']);
}
?>