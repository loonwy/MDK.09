<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['add'])){
    $email=trim($_POST['email']);
    $password=$_POST['password'];
    $company=trim($_POST['company_name']);
    $contact=trim($_POST['contact_person']);
    $phone=trim($_POST['phone']);
    $hash=password_hash($password,PASSWORD_DEFAULT);
    $stmt=$pdo->prepare("INSERT INTO users (email,password_hash,role,company_name,contact_person,phone,is_profile_completed) VALUES (?,?,'tenant',?,?,?,0)");
    $stmt->execute([$email,$hash,$company,$contact,$phone]);
    header("Location: tenants.php");
    exit;
}
if(isset($_GET['delete'])){
    $id=(int)$_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE id=? AND role='tenant'")->execute([$id]);
    header("Location: tenants.php");
    exit;
}
$tenants=$pdo->query("SELECT * FROM users WHERE role='tenant' ORDER BY id DESC")->fetchAll();
include '../includes/header.php'; ?>
<h2>Управление арендаторами</h2>
<button class="btn btn-success" onclick="document.getElementById('addForm').style.display='block'">+ Добавить арендатора</button>
<div id="addForm" style="display:none; margin-top:20px; border:1px solid #ccc; padding:15px;">
    <h3>Добавить арендатора</h3>
    <form method="post"><input type="hidden" name="add" value="1">
        <div class="form-group"><label>Email:</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Пароль:</label><input type="password" name="password" required></div>
        <div class="form-group"><label>Компания:</label><input type="text" name="company_name" required></div>
        <div class="form-group"><label>Контактное лицо:</label><input type="text" name="contact_person" required></div>
        <div class="form-group"><label>Телефон:</label><input type="text" name="phone" required></div>
        <button type="submit" class="btn btn-success">Сохранить</button>
        <button type="button" class="btn" onclick="document.getElementById('addForm').style.display='none'">Отмена</button>
    </form>
</div>
<div class="table-container">
    <table><thead><tr><th>ID</th><th>Email</th><th>Компания</th><th>ИНН</th><th>Телефон</th><th>Статус профиля</th><th>Действия</th></tr></thead>
    <tbody><?php foreach($tenants as $t):?>
    <tr><td><?=$t['id']?></td><td><?=htmlspecialchars($t['email'])?></td><td><?=htmlspecialchars($t['company_name'])?></td><td><?=htmlspecialchars($t['inn']??'—')?></td><td><?=htmlspecialchars($t['phone'])?></td><td><?=$t['is_profile_completed']?'✅ Заполнен':'❌ Не заполнен'?></td>
    <td><a href="?delete=<?=$t['id']?>" onclick="return confirm('Удалить?')" class="btn btn-sm btn-danger">Удалить</a></td></tr>
    <?php endforeach;?></tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>