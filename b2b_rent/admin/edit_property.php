<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();
$id = (int)$_GET['id'];
$prop = $pdo->prepare("SELECT * FROM properties WHERE id=?")->execute([$id]) ? $prop = $pdo->query("SELECT * FROM properties WHERE id=$id")->fetch() : null;
if(!$prop) { header("Location: properties.php"); exit; }

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['update'])){
    $name=trim($_POST['name']); $address=trim($_POST['address']); $area=(float)$_POST['area']; $rate=(float)$_POST['monthly_rate']; $status=$_POST['status']; $desc=trim($_POST['description']);
    $pdo->prepare("UPDATE properties SET name=?, address=?, area=?, monthly_rate=?, status=?, description=? WHERE id=?")->execute([$name,$address,$area,$rate,$status,$desc,$id]);
    header("Location: edit_property.php?id=$id");
}
if(isset($_GET['delete_image'])){
    $img_id=(int)$_GET['delete_image'];
    $img=$pdo->prepare("SELECT image_path FROM property_images WHERE id=?")->execute([$img_id]) ? $pdo->query("SELECT image_path FROM property_images WHERE id=$img_id")->fetchColumn() : '';
    if($img){ $file=$_SERVER['DOCUMENT_ROOT'].$img; if(file_exists($file)) unlink($file); }
    $pdo->prepare("DELETE FROM property_images WHERE id=?")->execute([$img_id]);
    header("Location: edit_property.php?id=$id");
}
if(isset($_GET['set_main'])){
    $img_id=(int)$_GET['set_main'];
    $pdo->prepare("UPDATE property_images SET is_main=0 WHERE property_id=?")->execute([$id]);
    $pdo->prepare("UPDATE property_images SET is_main=1 WHERE id=?")->execute([$img_id]);
    header("Location: edit_property.php?id=$id");
}
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['add_images'])){
    if(!empty($_FILES['new_images']['name'][0])){
        $upload_dir=$_SERVER['DOCUMENT_ROOT'].'/b2b_rent/uploads/properties/';
        if(!is_dir($upload_dir)) mkdir($upload_dir,0777,true);
        $maxSort=$pdo->prepare("SELECT COALESCE(MAX(sort_order),-1) FROM property_images WHERE property_id=?")->execute([$id]) ? $pdo->query("SELECT COALESCE(MAX(sort_order),-1) FROM property_images WHERE property_id=$id")->fetchColumn() : -1;
        $sort=$maxSort+1;
        foreach($_FILES['new_images']['tmp_name'] as $key=>$tmp){
            if($_FILES['new_images']['error'][$key]!==UPLOAD_ERR_OK) continue;
            $ext=strtolower(pathinfo($_FILES['new_images']['name'][$key],PATHINFO_EXTENSION));
            $new_name=uniqid().'_'.time().'.'.$ext;
            move_uploaded_file($tmp,$upload_dir.$new_name);
            $pdo->prepare("INSERT INTO property_images (property_id, image_path, is_main, sort_order) VALUES (?,?,0,?)")->execute([$id,'/b2b_rent/uploads/properties/'.$new_name,$sort++]);
        }
        header("Location: edit_property.php?id=$id");
    }
}
$images=$pdo->prepare("SELECT * FROM property_images WHERE property_id=? ORDER BY is_main DESC, sort_order ASC")->execute([$id]) ? $pdo->query("SELECT * FROM property_images WHERE property_id=$id ORDER BY is_main DESC, sort_order ASC")->fetchAll() : [];
include '../includes/header.php'; ?>
<h1>Редактирование: <?=htmlspecialchars($prop['name'])?></h1>
<form method="post"><input type="hidden" name="update" value="1">
    <div class="form-group"><label>Название</label><input type="text" name="name" value="<?=htmlspecialchars($prop['name'])?>" required></div>
    <div class="form-group"><label>Адрес</label><textarea name="address" required><?=htmlspecialchars($prop['address'])?></textarea></div>
    <div class="form-group"><label>Площадь (м²)</label><input type="number" step="0.01" name="area" value="<?=$prop['area']?>" required></div>
    <div class="form-group"><label>Ставка (руб/мес)</label><input type="number" step="0.01" name="monthly_rate" value="<?=$prop['monthly_rate']?>" required></div>
    <div class="form-group"><label>Статус</label><select name="status"><option value="free" <?=$prop['status']=='free'?'selected':''?>>Свободно</option><option value="rented" <?=$prop['status']=='rented'?'selected':''?>>Арендовано</option><option value="repair" <?=$prop['status']=='repair'?'selected':''?>>Ремонт</option></select></div>
    <div class="form-group"><label>Описание</label><textarea name="description"><?=htmlspecialchars($prop['description'])?></textarea></div>
    <button type="submit" class="btn btn-success">Сохранить</button> <a href="properties.php" class="btn">Отмена</a>
</form>
<h2>Изображения</h2>
<div style="display:flex; flex-wrap:wrap; gap:20px;"><?php foreach($images as $img):?>
    <div style="border:1px solid #ddd; padding:10px; width:150px; text-align:center;">
        <img src="<?=$img['image_path']?>" style="width:100%; height:100px; object-fit:cover;">
        <?php if($img['is_main']):?><strong>Обложка</strong><br><?php else:?><a href="?id=<?=$id?>&set_main=<?=$img['id']?>" class="btn btn-sm btn-warning">Сделать обложкой</a><br><?php endif;?>
        <a href="?id=<?=$id?>&delete_image=<?=$img['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">Удалить</a>
    </div>
<?php endforeach;?></div>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="add_images" value="1"><div class="form-group"><label>Добавить изображения</label><input type="file" name="new_images[]" multiple accept="image/jpeg,image/png,image/webp"></div><button type="submit" class="btn btn-success">Загрузить</button></form>
<?php include '../includes/footer.php'; ?>