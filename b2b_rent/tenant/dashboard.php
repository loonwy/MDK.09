<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn();
if($_SESSION['role']!='tenant') header("Location: ../index.php");
$user_id=$_SESSION['user_id'];
$error=''; $success=''; $edit_mode=isset($_GET['edit']);
$user=$pdo->prepare("SELECT * FROM users WHERE id=?")->execute([$user_id])?$pdo->query("SELECT * FROM users WHERE id=$user_id")->fetch():null;
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['save_profile'])){
    $company=trim($_POST['company_name']); $inn=trim($_POST['inn']); $kpp=trim($_POST['kpp']); $address=trim($_POST['legal_address']);
    $bank=trim($_POST['bank_name']); $acc=trim($_POST['bank_account']); $corr=trim($_POST['corr_account']); $bik=trim($_POST['bik']);
    $contact=trim($_POST['contact_person']); $phone=trim($_POST['phone']);
    $errors=[];
    if(empty($company)) $errors[]='Название компании обязательно';
    if(empty($inn)||(strlen($inn)!=10 && strlen($inn)!=12)) $errors[]='ИНН 10 или 12 цифр';
    if(empty($address)) $errors[]='Юридический адрес обязателен';
    if(empty($bank)) $errors[]='Название банка обязательно';
    if(empty($acc)||strlen($acc)!=20) $errors[]='Расчётный счёт 20 цифр';
    if(empty($bik)||strlen($bik)!=9) $errors[]='БИК 9 цифр';
    if(empty($contact)) $errors[]='Контактное лицо';
    if(empty($phone)) $errors[]='Телефон';
    if(empty($errors)){
        $stmt=$pdo->prepare("UPDATE users SET company_name=?, inn=?, kpp=?, legal_address=?, bank_name=?, bank_account=?, corr_account=?, bik=?, contact_person=?, phone=?, is_profile_completed=1 WHERE id=?");
        $stmt->execute([$company,$inn,$kpp,$address,$bank,$acc,$corr,$bik,$contact,$phone,$user_id]);
        $_SESSION['company']=$company; $_SESSION['is_profile_completed']=1;
        $success='Профиль сохранён'; $edit_mode=false;
        $user=$pdo->prepare("SELECT * FROM users WHERE id=?")->execute([$user_id])?$pdo->query("SELECT * FROM users WHERE id=$user_id")->fetch():null;
    } else $error=implode('<br>',$errors);
}
$is_completed=$user['is_profile_completed']??0;
$show_profile_form = !$is_completed || $edit_mode;
if(!$is_completed && !$edit_mode) $show_profile_form=true;
if($is_completed && !$edit_mode){
    $active_contracts=$pdo->prepare("SELECT c.*, p.name as pname, p.address FROM contracts c JOIN properties p ON c.property_id=p.id WHERE c.tenant_id=? AND c.status='active'")->execute([$user_id])?$pdo->query("SELECT c.*, p.name as pname, p.address FROM contracts c JOIN properties p ON c.property_id=p.id WHERE c.tenant_id=$user_id AND c.status='active'")->fetchAll():[];
    $recent_requests=$pdo->prepare("SELECT * FROM contract_requests WHERE tenant_id=? ORDER BY created_at DESC LIMIT 5")->execute([$user_id])?$pdo->query("SELECT * FROM contract_requests WHERE tenant_id=$user_id ORDER BY created_at DESC LIMIT 5")->fetchAll():[];
}
include '../includes/header.php'; ?>
<?php if($success):?><div class="alert alert-success"><?=$success?></div><?php endif;?>
<?php if($error):?><div class="alert alert-error"><?=$error?></div><?php endif;?>
<?php if($show_profile_form):?>
<h1><?=$edit_mode?'Редактирование профиля':'Заполнение профиля'?></h1>
<?php if(!$edit_mode && !$is_completed):?><div class="alert alert-warning">Для подачи заявок заполните реквизиты.</div><?php endif;?>
<form method="post"><input type="hidden" name="save_profile" value="1">
    <div class="form-group"><label>Название компании *</label><input type="text" name="company_name" value="<?=htmlspecialchars($user['company_name']??'')?>" required></div>
    <div class="form-group"><label>ИНН * (10 или 12 цифр)</label><input type="text" name="inn" value="<?=htmlspecialchars($user['inn']??'')?>" required pattern="\d{10}|\d{12}"></div>
    <div class="form-group"><label>КПП (9 цифр)</label><input type="text" name="kpp" value="<?=htmlspecialchars($user['kpp']??'')?>" pattern="\d{9}"></div>
    <div class="form-group"><label>Юридический адрес *</label><textarea name="legal_address" required><?=htmlspecialchars($user['legal_address']??'')?></textarea></div>
    <div class="form-group"><label>Название банка *</label><input type="text" name="bank_name" value="<?=htmlspecialchars($user['bank_name']??'')?>" required></div>
    <div class="form-group"><label>Расчётный счёт * (20 цифр)</label><input type="text" name="bank_account" value="<?=htmlspecialchars($user['bank_account']??'')?>" required pattern="\d{20}"></div>
    <div class="form-group"><label>Корр. счёт (20 цифр)</label><input type="text" name="corr_account" value="<?=htmlspecialchars($user['corr_account']??'')?>" pattern="\d{20}"></div>
    <div class="form-group"><label>БИК * (9 цифр)</label><input type="text" name="bik" value="<?=htmlspecialchars($user['bik']??'')?>" required pattern="\d{9}"></div>
    <div class="form-group"><label>Контактное лицо *</label><input type="text" name="contact_person" value="<?=htmlspecialchars($user['contact_person']??'')?>" required></div>
    <div class="form-group"><label>Телефон *</label><input type="tel" name="phone" value="<?=htmlspecialchars($user['phone']??'')?>" required></div>
    <button type="submit" class="btn btn-success">Сохранить</button>
    <?php if($edit_mode):?><a href="dashboard.php" class="btn">Отмена</a><?php endif;?>
</form>
<?php else:?>
<h1>Добро пожаловать, <?=htmlspecialchars($_SESSION['company'])?></h1>
<p><a href="?edit=1" class="btn btn-warning">Редактировать профиль</a></p>
<h2>Активные договоры</h2>
<?php if(count($active_contracts)==0):?><div class="alert alert-info">Нет активных договоров. <a href="properties.php">Найти объект</a></div>
<?php else:?><div class="table-container"><table><thead><tr><th>Объект</th><th>Адрес</th><th>Период</th><th>Ставка</th></tr></thead><tbody><?php foreach($active_contracts as $c):?><tr><td><?=htmlspecialchars($c['pname'])?></td><td><?=htmlspecialchars($c['address'])?></td><td><?=$c['start_date']?> – <?=$c['end_date']?></td><td><?=number_format($c['monthly_rent'],0,',',' ')?> руб.</td></tr><?php endforeach;?></tbody></table></div><?php endif;?>
<h2>Последние заявки</h2>
<?php if(count($recent_requests)==0):?><div class="alert alert-info">Вы ещё не подавали заявок.</div>
<?php else:?><div class="table-container"><table><thead><tr><th>Объект</th><th>Период</th><th>Статус</th><th>Дата</th></tr></thead><tbody><?php foreach($recent_requests as $r):
    $pname=$pdo->prepare("SELECT name FROM properties WHERE id=?")->execute([$r['property_id']])?$pdo->query("SELECT name FROM properties WHERE id={$r['property_id']}")->fetchColumn():'';
?><tr><td><?=htmlspecialchars($pname)?></td><td><?=$r['start_date']?> – <?=$r['end_date']?></td><td><?php if($r['status']=='pending'):?>⏳ На рассмотрении<?php elseif($r['status']=='approved'):?>✅ Одобрена<?php else:?>❌ Отклонена<?php endif;?></td><td><?=$r['created_at']?></td></tr><?php endforeach;?></tbody></table><a href="my_requests.php" class="btn">Все заявки</a></div><?php endif;?>
<?php endif; include '../includes/footer.php'; ?>