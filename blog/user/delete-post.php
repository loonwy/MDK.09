<?php
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT image_path FROM posts WHERE id = ? AND author_id = ?");
$stmt->execute([$post_id, $_SESSION['user_id']]);
$post = $stmt->fetch();

if ($post) {
    if (!empty($post['image_path'])) {
        $image = '../' . $post['image_path'];
        if (file_exists($image)) {
            unlink($image);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
}

header('Location: my-posts.php?deleted=1');
exit;
?>