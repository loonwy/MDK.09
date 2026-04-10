<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn();
if($_SESSION['role']!='tenant') header("Location: ../index.php");
if(!isProfileCompleted($_SESSION['user_id'],$pdo)){
    $_SESSION['error']="Заполните профиль";
    header("Location: dashboard.php");
    exit;
}
$property_id=(int)$_GET['property_id'];
$prop=$pdo->prepare("SELECT * FROM properties WHERE id=? AND status='free'")->execute([$property_id])?$pdo->query("SELECT * FROM properties WHERE id=$property_id AND status='free'")->fetch():null;
if(!$prop){ $_SESSION['error']="Объект не найден"; header("Location: properties.php"); exit; }
$error=''; $success='';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $start=$_POST['start_date']; $end=$_POST['end_date']; $msg=trim($_POST['message']);
    if(empty($start)||empty($end)) $error="Укажите даты";
    elseif($start<date('Y-m-d')) $error="Дата начала не может быть в прошлом";
    elseif($end<=$start) $error="Дата окончания должна быть позже";
    else{
        $stmt=$pdo->prepare("INSERT INTO contract_requests (tenant_id, property_id, start_date, end_date, message) VALUES (?,?,?,?,?)");
        $stmt->execute([$_SESSION['user_id'],$property_id,$start,$end,$msg]);
        $success="Заявка отправлена";
    }
}
include '../includes/header.php'; ?>
<h1>Предложение контракта</h1>
<h3><?=htmlspecialchars($prop['name'])?></h3>
<p>Ставка: <?=number_format($prop['monthly_rate'],0,',',' ')?> руб./мес</p>
<?php if($success):?><div class="alert alert-success"><?=$success?></div><a href="properties.php" class="btn">Вернуться</a><?php else:?>
<?php if($error) echo '<div class="alert alert-error">'.$error.'</div>'; ?>
<form method="post"><div class="form-group"><label>Дата начала:</label><input type="date" name="start_date" required></div><div class="form-group"><label>Дата окончания:</label><input type="date" name="end_date" required></div><div class="form-group"><label>Комментарий:</label><textarea name="message"></textarea></div><button type="submit" class="btn btn-success">Отправить</button><a href="properties.php" class="btn btn-danger">Отмена</a></form>
<?php endif; ?>
<?php include '../includes/footer.php'; ?>