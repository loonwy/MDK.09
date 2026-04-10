<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['action'],$_POST['request_id'])){
    $req_id=(int)$_POST['request_id'];
    $action=$_POST['action'];
    $req=$pdo->prepare("SELECT * FROM contract_requests WHERE id=?")->execute([$req_id])?$pdo->query("SELECT * FROM contract_requests WHERE id=$req_id")->fetch():null;
    if($req){
        if($action=='approve'){
            $prop=$pdo->prepare("SELECT status, monthly_rate FROM properties WHERE id=?")->execute([$req['property_id']])?$pdo->query("SELECT status, monthly_rate FROM properties WHERE id={$req['property_id']}")->fetch():null;
            if($prop && $prop['status']=='free'){
                $stmt=$pdo->prepare("INSERT INTO contracts (property_id,tenant_id,start_date,end_date,monthly_rent,status) VALUES (?,?,?,?,?,'active')");
                $stmt->execute([$req['property_id'],$req['tenant_id'],$req['start_date'],$req['end_date'],$prop['monthly_rate']]);
                $pdo->prepare("UPDATE properties SET status='rented' WHERE id=?")->execute([$req['property_id']]);
                $pdo->prepare("UPDATE contract_requests SET status='approved' WHERE id=?")->execute([$req_id]);
            } else {
                $pdo->prepare("UPDATE contract_requests SET status='rejected' WHERE id=?")->execute([$req_id]);
            }
        } elseif($action=='reject'){
            $pdo->prepare("UPDATE contract_requests SET status='rejected' WHERE id=?")->execute([$req_id]);
        }
    }
    header("Location: requests.php");
    exit;
}
$requests=$pdo->query("SELECT r.*, u.company_name, u.email, p.name as pname, p.address FROM contract_requests r JOIN users u ON r.tenant_id=u.id JOIN properties p ON r.property_id=p.id ORDER BY r.created_at DESC")->fetchAll();
include '../includes/header.php'; ?>
<h1>Управление заявками</h1>
<?php if(count($requests)==0):?><div class="alert alert-info">Нет заявок.</div><?php else:?>
<div class="table-container"><table><thead><tr><th>ID</th><th>Арендатор</th><th>Объект</th><th>Период</th><th>Комментарий</th><th>Статус</th><th>Действия</th></tr></thead>
<tbody><?php foreach($requests as $r):?><tr><td><?=$r['id']?></td><td><?=htmlspecialchars($r['company_name'])?><br><small><?=$r['email']?></small></td><td><?=htmlspecialchars($r['pname'])?></td><td><?=$r['start_date']?> – <?=$r['end_date']?></td><td><?=nl2br(htmlspecialchars($r['message']))?></td><td><?php if($r['status']=='pending'):?>⏳ На рассмотрении<?php elseif($r['status']=='approved'):?>✅ Одобрена<?php else:?>❌ Отклонена<?php endif;?></td>
<td><?php if($r['status']=='pending'):?><form method="post" style="display:inline;"><input type="hidden" name="request_id" value="<?=$r['id']?>"><button type="submit" name="action" value="approve" class="btn btn-sm btn-success">Одобрить</button> <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">Отклонить</button></form><?php else:?>—<?php endif;?></td></tr><?php endforeach;?></tbody></table></div>
<?php endif; include '../includes/footer.php'; ?>