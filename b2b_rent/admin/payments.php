<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['add'])){
    $contract_id=(int)$_POST['contract_id'];
    $amount=(float)$_POST['amount'];
    $payment_date=$_POST['payment_date']?:null;
    $due_date=$_POST['due_date'];
    $status=$_POST['status'];
    $stmt=$pdo->prepare("INSERT INTO payments (contract_id,amount,payment_date,due_date,status) VALUES (?,?,?,?,?)");
    $stmt->execute([$contract_id,$amount,$payment_date,$due_date,$status]);
    header("Location: payments.php");
    exit;
}
if(isset($_GET['delete'])){
    $id=(int)$_GET['delete'];
    $pdo->prepare("DELETE FROM payments WHERE id=?")->execute([$id]);
    header("Location: payments.php");
    exit;
}
$payments=$pdo->query("SELECT p.*, c.property_id, pr.name as pname, u.company_name as tname FROM payments p JOIN contracts c ON p.contract_id=c.id JOIN properties pr ON c.property_id=pr.id JOIN users u ON c.tenant_id=u.id ORDER BY p.due_date DESC")->fetchAll();
$contracts=$pdo->query("SELECT c.id, pr.name as pname, u.company_name as tname FROM contracts c JOIN properties pr ON c.property_id=pr.id JOIN users u ON c.tenant_id=u.id WHERE c.status='active'")->fetchAll();
include '../includes/header.php'; ?>
<h2>Управление платежами</h2>
<button class="btn btn-success" onclick="document.getElementById('addForm').style.display='block'">+ Добавить платёж</button>
<div id="addForm" style="display:none; margin-top:20px; border:1px solid #ccc; padding:15px;">
    <form method="post"><input type="hidden" name="add" value="1">
        <div class="form-group"><label>Договор:</label><select name="contract_id" required><?php foreach($contracts as $c):?><option value="<?=$c['id']?>"><?=htmlspecialchars($c['pname'].' — '.$c['tname'])?></option><?php endforeach;?></select></div>
        <div class="form-group"><label>Сумма (руб):</label><input type="number" step="0.01" name="amount" required></div>
        <div class="form-group"><label>Дата оплаты (оставьте пустым если не оплачено):</label><input type="date" name="payment_date"></div>
        <div class="form-group"><label>Срок оплаты:</label><input type="date" name="due_date" required></div>
        <div class="form-group"><label>Статус:</label><select name="status"><option value="pending">Ожидает</option><option value="paid">Оплачено</option><option value="overdue">Просрочено</option></select></div>
        <button type="submit" class="btn btn-success">Сохранить</button>
        <button type="button" class="btn" onclick="document.getElementById('addForm').style.display='none'">Отмена</button>
    </form>
</div>
<div class="table-container"><table><thead><tr><th>ID</th><th>Объект</th><th>Арендатор</th><th>Сумма</th><th>Дата оплаты</th><th>Срок</th><th>Статус</th><th>Действия</th></tr></thead>
<tbody><?php foreach($payments as $p):?><tr><td><?=$p['id']?></td><td><?=htmlspecialchars($p['pname'])?></td><td><?=htmlspecialchars($p['tname'])?></td><td><?=$p['amount']?> руб.</td><td><?=$p['payment_date']??'—'?></td><td><?=$p['due_date']?></td><td><?=$p['status']?></td><td><a href="?delete=<?=$p['id']?>" onclick="return confirm('Удалить?')" class="btn btn-sm btn-danger">Удалить</a></td></tr><?php endforeach;?></tbody></table></div>
<?php include '../includes/footer.php'; ?>