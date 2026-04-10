<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['add'])){
    $property_id=(int)$_POST['property_id'];
    $tenant_id=(int)$_POST['tenant_id'];
    $start=$_POST['start_date'];
    $end=$_POST['end_date'];
    $rent=(float)$_POST['monthly_rent'];
    $conditions=trim($_POST['special_conditions']);
    $status=$_POST['status'];
    $stmt=$pdo->prepare("INSERT INTO contracts (property_id,tenant_id,start_date,end_date,monthly_rent,special_conditions,status) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([$property_id,$tenant_id,$start,$end,$rent,$conditions,$status]);
    $pdo->prepare("UPDATE properties SET status='rented' WHERE id=?")->execute([$property_id]);
    header("Location: contracts.php");
    exit;
}
if(isset($_GET['delete'])){
    $id=(int)$_GET['delete'];
    $pdo->prepare("DELETE FROM contracts WHERE id=?")->execute([$id]);
    header("Location: contracts.php");
    exit;
}
$contracts=$pdo->query("SELECT c.*, p.name as pname, u.company_name as tname FROM contracts c JOIN properties p ON c.property_id=p.id JOIN users u ON c.tenant_id=u.id ORDER BY c.id DESC")->fetchAll();
$properties=$pdo->query("SELECT id,name FROM properties WHERE status='free' OR status='rented'")->fetchAll();
$tenants=$pdo->query("SELECT id,company_name FROM users WHERE role='tenant'")->fetchAll();
include '../includes/header.php'; ?>
<h2>Управление договорами</h2>
<button class="btn btn-success" onclick="document.getElementById('addForm').style.display='block'">+ Добавить договор</button>
<div id="addForm" style="display:none; margin-top:20px; border:1px solid #ccc; padding:15px;">
    <form method="post"><input type="hidden" name="add" value="1">
        <div class="form-group"><label>Объект:</label><select name="property_id" required><?php foreach($properties as $p):?><option value="<?=$p['id']?>"><?=htmlspecialchars($p['name'])?></option><?php endforeach;?></select></div>
        <div class="form-group"><label>Арендатор:</label><select name="tenant_id" required><?php foreach($tenants as $t):?><option value="<?=$t['id']?>"><?=htmlspecialchars($t['company_name'])?></option><?php endforeach;?></select></div>
        <div class="form-group"><label>Дата начала:</label><input type="date" name="start_date" required></div>
        <div class="form-group"><label>Дата окончания:</label><input type="date" name="end_date" required></div>
        <div class="form-group"><label>Ежемесячная аренда (руб):</label><input type="number" step="0.01" name="monthly_rent" required></div>
        <div class="form-group"><label>Особые условия:</label><textarea name="special_conditions"></textarea></div>
        <div class="form-group"><label>Статус:</label><select name="status"><option value="active">Активен</option><option value="expired">Истёк</option><option value="terminated">Расторгнут</option></select></div>
        <button type="submit" class="btn btn-success">Сохранить</button>
        <button type="button" class="btn" onclick="document.getElementById('addForm').style.display='none'">Отмена</button>
    </form>
</div>
<div class="table-container"><table><thead><tr><th>ID</th><th>Объект</th><th>Арендатор</th><th>Период</th><th>Ставка</th><th>Статус</th><th>Действия</th></tr></thead>
<tbody><?php foreach($contracts as $c):?><tr><td><?=$c['id']?></td><td><?=htmlspecialchars($c['pname'])?></td><td><?=htmlspecialchars($c['tname'])?></td><td><?=$c['start_date']?> – <?=$c['end_date']?></td><td><?=$c['monthly_rent']?> руб.</td><td><?=$c['status']?></td><td><a href="?delete=<?=$c['id']?>" onclick="return confirm('Удалить договор?')" class="btn btn-sm btn-danger">Удалить</a></td></tr><?php endforeach;?></tbody></table></div>
<?php include '../includes/footer.php'; ?>