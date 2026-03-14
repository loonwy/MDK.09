<?php
session_start();
header('Content-Type: application/json');

require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Не авторизован']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Неверный метод']);
    exit;
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

if (!$post_id || empty($comment)) {
    echo json_encode(['success' => false, 'error' => 'Заполните все поля']);
    exit;
}


$stmt = $pdo->prepare("
    SELECT id FROM comments 
    WHERE post_id = ? AND user_id = ? AND comment = ? AND created_at > DATE_SUB(NOW(), INTERVAL 10 SECOND)
");
$stmt->execute([$post_id, $_SESSION['user_id'], $comment]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Комментарий уже отправлен']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $_SESSION['user_id'], $comment]);

    $comment_id = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("
        SELECT c.*, u.username 
        FROM comments c
        JOIN users u ON c.user_id = u.id 
        WHERE c.id = ?
    ");
    $stmt->execute([$comment_id]);
    $new_comment = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'comment' => [
            'id' => $new_comment['id'],
            'username' => $new_comment['username'],
            'comment' => nl2br(htmlspecialchars($new_comment['comment'])),
            'created_at' => date('d.m.Y H:i', strtotime($new_comment['created_at']))
        ]
    ]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Ошибка сохранения']);
}
?>