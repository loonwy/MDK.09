<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();
if(!isset($_GET['property_id'])) die(json_encode([]));
$pid=(int)$_GET['property_id'];
$stmt=$pdo->prepare("SELECT image_path FROM property_images WHERE property_id=? ORDER BY is_main DESC, sort_order ASC");
$stmt->execute([$pid]);
$images=$stmt->fetchAll(PDO::FETCH_COLUMN);
header('Content-Type: application/json');
echo json_encode($images);
?>