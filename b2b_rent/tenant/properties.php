<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();
if ($_SESSION['role'] != 'tenant') {
    header("Location: ../index.php");
    exit;
}

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$totalStmt = $pdo->query("SELECT COUNT(*) FROM properties WHERE status='free'");
$total = $totalStmt->fetchColumn();
$total_pages = ceil($total / $limit);

$stmt = $pdo->prepare("SELECT * FROM properties WHERE status='free' ORDER BY id DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$properties = $stmt->fetchAll();

function getCoverImage($property_id, $pdo) {
    $stmt = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id=? AND is_main=1 LIMIT 1");
    $stmt->execute([$property_id]);
    $img = $stmt->fetchColumn();
    if(!$img) {
        $stmt = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id=? LIMIT 1");
        $stmt->execute([$property_id]);
        $img = $stmt->fetchColumn();
    }
    return $img ?: '/b2b_rent/assets/images/no-image.png';
}

include '../includes/header.php';
?>

<h1>Доступные объекты для аренды</h1>

<?php if (count($properties) == 0): ?>
    <div class="alert alert-info">На данный момент нет свободных объектов.</div>
<?php else: ?>
    <div class="properties-grid">
        <?php foreach ($properties as $p): 
            $cover = getCoverImage($p['id'], $pdo);
        ?>
            <a href="property_detail.php?id=<?= $p['id'] ?>" class="card property-card" style="text-decoration: none; color: inherit;">
                <img src="<?= $cover ?>" style="width:100%; height:200px; object-fit:cover;" alt="<?= htmlspecialchars($p['name']) ?>">
                <div class="card-body">
                    <h3><?= htmlspecialchars($p['name']) ?></h3>
                    <p><strong>Адрес:</strong> <?= htmlspecialchars($p['address']) ?></p>
                    <p class="card-price"><?= number_format($p['monthly_rate'], 0, ',', ' ') ?> руб./мес</p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="btn btn-sm <?= $i == $page ? 'btn-success' : 'btn' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>