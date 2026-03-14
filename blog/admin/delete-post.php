<?php
require_once '../config/db.php';
require_once 'check-admin.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    $stmt = $pdo->prepare("SELECT image_path FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    
    if ($post && !empty($post['image_path'])) {
        $image = '../' . $post['image_path'];
        if (file_exists($image)) unlink($image);
    }
    
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: posts.php');
exit;
?>