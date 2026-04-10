<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();
if ($_SESSION['role'] != 'tenant') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: properties.php");
    exit;
}
$property_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$property_id]);
$property = $stmt->fetch();
if (!$property) {
    header("Location: properties.php");
    exit;
}

$stmt = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id = ? ORDER BY is_main DESC, sort_order ASC");
$stmt->execute([$property_id]);
$images = $stmt->fetchAll(PDO::FETCH_COLUMN);
if (empty($images)) {
    $images = ['/b2b_rent/assets/images/no-image.png'];
}

include '../includes/header.php';
?>

<style>
    .detail-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px 0;
    }
    .carousel {
        position: relative;
        background: #f0f2f5;
        border-radius: 32px;
        overflow: hidden;
        margin-bottom: 30px;
        box-shadow: 0 8px 24px rgba(0,38,51,0.08);
    }
    .carousel-inner {
        position: relative;
        width: 100%;
        height: 500px;
        background: #f0f2f5;
    }
    .carousel-inner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .carousel-control {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 44px;
        height: 44px;
        background: rgba(0,38,51,0.5);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: bold;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
        z-index: 10;
        backdrop-filter: blur(4px);
        user-select: none;
        font-family: monospace;
        line-height: 1;
    }
    .carousel-control:hover {
        background: #A6C8E1;
        color: #002633;
    }
    .carousel-control.prev { left: 20px; }
    .carousel-control.next { right: 20px; }
    .carousel-indicators {
        position: absolute;
        bottom: 15px;
        left: 0;
        right: 0;
        text-align: center;
        z-index: 10;
    }
    .carousel-indicators span {
        display: inline-block;
        width: 10px;
        height: 10px;
        background: rgba(255,255,255,0.6);
        border-radius: 50%;
        margin: 0 5px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .carousel-indicators span.active {
        background: #A6C8E1;
        width: 12px;
        height: 12px;
    }
    .info-card {
        background: #FFFFFF;
        border-radius: 32px;
        padding: 32px;
        box-shadow: 0 8px 24px rgba(0,38,51,0.08);
        margin-top: 20px;
    }
    .property-title {
        font-size: 2rem;
        font-weight: 700;
        color: #002633;
        margin-bottom: 8px;
    }
    .property-address {
        font-size: 1.1rem;
        color: #4a5b6e;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .property-price {
        font-size: 2rem;
        font-weight: 700;
        color: #002633;
        background: #E6F2F7;
        display: inline-block;
        padding: 8px 24px;
        border-radius: 40px;
        margin-bottom: 20px;
    }
    .detail-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin: 20px 0;
        padding-top: 20px;
        border-top: 1px solid #A6C8E1;
    }
    .detail-item {
        flex: 1;
        min-width: 150px;
    }
    .detail-item strong {
        display: block;
        font-size: 0.9rem;
        color: #4a5b6e;
        margin-bottom: 4px;
    }
    .detail-item span {
        font-size: 1.2rem;
        font-weight: 700;
        color: #002633;
    }
    .description {
        margin: 20px 0;
        line-height: 1.6;
    }
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        flex-wrap: wrap;
    }
    .btn-back {
        background: #A6C8E1;
        color: #002633;
    }
    .btn-back:hover {
        background: #002633;
        color: #FFFFFF;
    }
    @media (max-width: 768px) {
        .carousel-inner { height: 300px; }
        .carousel-control { width: 36px; height: 36px; font-size: 28px; }
        .property-title { font-size: 1.5rem; }
        .property-price { font-size: 1.5rem; }
        .info-card { padding: 20px; }
    }
</style>

<div class="detail-page">
    <!-- Карусель изображений -->
    <div class="carousel">
        <div class="carousel-inner" id="carouselInner">
            <img id="mainImage" src="<?= $images[0] ?>" alt="Фото объекта">
        </div>
        <?php if (count($images) > 1): ?>
            <div class="carousel-control prev" id="prevBtn">‹</div>
            <div class="carousel-control next" id="nextBtn">›</div>
            <div class="carousel-indicators" id="indicators"></div>
        <?php endif; ?>
    </div>

    <!-- Информация об объекте -->
    <div class="info-card">
        <h1 class="property-title"><?= htmlspecialchars($property['name']) ?></h1>
        <div class="property-address">
            <span>📍</span> <?= htmlspecialchars($property['address']) ?>
        </div>
        <div class="property-price">
            <?= number_format($property['monthly_rate'], 0, ',', ' ') ?> ₽ / месяц
        </div>
        
        <div class="detail-row">
            <div class="detail-item">
                <strong>Площадь</strong>
                <span><?= $property['area'] ?> м²</span>
            </div>
            <div class="detail-item">
                <strong>Статус</strong>
                <span><?= $property['status'] == 'free' ? 'Свободно' : 'Арендовано' ?></span>
            </div>
        </div>

        <div class="description">
            <strong>Описание</strong><br>
            <?= nl2br(htmlspecialchars($property['description'])) ?>
        </div>

        <div class="action-buttons">
            <a href="request_contract.php?property_id=<?= $property_id ?>" class="btn">📝 Предложить контракт</a>
            <a href="properties.php" class="btn btn-back">← Назад к списку</a>
        </div>
    </div>
</div>

<script>
    const images = <?= json_encode($images) ?>;
    let currentIndex = 0;
    const mainImage = document.getElementById('mainImage');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const indicatorsContainer = document.getElementById('indicators');

    function updateCarousel() {
        mainImage.src = images[currentIndex];
        if (indicatorsContainer) {
            document.querySelectorAll('.carousel-indicators span').forEach((dot, idx) => {
                if (idx === currentIndex) dot.classList.add('active');
                else dot.classList.remove('active');
            });
        }
    }

    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateCarousel();
        });
        nextBtn.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % images.length;
            updateCarousel();
        });
        if (indicatorsContainer) {
            images.forEach((_, idx) => {
                const dot = document.createElement('span');
                if (idx === 0) dot.classList.add('active');
                dot.addEventListener('click', () => {
                    currentIndex = idx;
                    updateCarousel();
                });
                indicatorsContainer.appendChild(dot);
            });
        }
    }
</script>

<?php include '../includes/footer.php'; ?>