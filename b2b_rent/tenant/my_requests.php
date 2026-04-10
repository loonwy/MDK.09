<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();
if($_SESSION['role']!='tenant') header("Location: ../index.php");
$user_id=$_SESSION['user_id'];
$requests=$pdo->prepare("SELECT r.*, p.name as pname FROM contract_requests r JOIN properties p ON r.property_id=p.id WHERE r.tenant_id=? ORDER BY r.created_at DESC")->execute([$user_id])?$pdo->query("SELECT r.*, p.name as pname FROM contract_requests r JOIN properties p ON r.property_id=p.id WHERE r.tenant_id=$user_id ORDER BY r.created_at DESC")->fetchAll():[];
include '../includes/header.php'; ?>
<h1>Мои заявки на аренду</h1>
<?php if(count($requests)==0):?><div class="alert alert-info">Вы ещё не подавали заявок. <a href="properties.php">Посмотреть объекты</a></div>
<?php else:?><div class="table-container"><table><thead><tr><th>Объект</th><th>Период</th><th>Комментарий</th><th>Статус</th><th>Дата</th></tr></thead><tbody><?php foreach($requests as $r):?><tr><td><?=htmlspecialchars($r['pname'])?></td><td><?=$r['start_date']?> – <?=$r['end_date']?></td><td><?=nl2br(htmlspecialchars($r['message']))?></td><td><?php if($r['status']=='pending'):?>⏳ На рассмотрении<?php elseif($r['status']=='approved'):?>✅ Одобрена<?php else:?>❌ Отклонена<?php endif;?></td><td><?=$r['created_at']?></td></tr><?php endforeach;?></tbody></table></div><?php endif; include '../includes/footer.php'; ?>