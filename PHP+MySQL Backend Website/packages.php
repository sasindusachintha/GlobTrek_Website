<?php
define('IN_SITE', true);
require_once __DIR__ . '/functions.php';
$packages = fetchAllPackages();
$activePage = 'packages';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packages | GlobeTrek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include __DIR__ . '/header.php'; ?>

    <section class="container mt-5 pt-4">
        <h2 class="section-title text-start mb-4">Explore Packages</h2>
        <div class="row g-4" id="package-container">
            <?php foreach ($packages as $pkg): ?>
                <div class="col-lg-3 col-md-6" data-category="<?= h($pkg['category'] ?? 'all') ?>">
                    <div class="package-card shadow-sm border rounded-3 overflow-hidden bg-white h-100">
                        <a href="package-details.php?id=<?= h($pkg['package_id']) ?>" class="text-decoration-none text-dark">
                            <div class="package-img"
                                style="background-image:url('<?= h($pkg['image'] ?: 'images/hero1.jpg') ?>');height:200px;background-size:cover;background-position:center;"></div>
                            <div class="p-3">
                                <h3 class="h5 fw-bold mb-1"><?= h($pkg['title']) ?></h3>
                                <p class="text-muted small mb-1"><i class="fa-solid fa-location-dot"></i> <?= h($pkg['location'] ?? '') ?></p>
                                <p class="text-warning mb-1">⭐ <?= h($pkg['rating'] ?? '') ?></p>
                                <p class="fw-bold text-success mb-2">LKR <?= number_format($pkg['price'], 0, '.', ',') ?></p>
                                <span class="badge <?= ($pkg['availability'] ?? 'available') === 'available' ? 'bg-success' : 'bg-secondary' ?>"><?= ($pkg['availability'] ?? 'available') === 'available' ? 'Available' : 'Unavailable' ?></span>
                            </div>
                        </a>
                        <div class="p-3 pt-0 d-flex justify-content-between align-items-center">
                            <button class="btn btn-primary btn-sm rounded-pill" <?= ($pkg['availability'] ?? 'available') === 'available' ? '' : 'disabled' ?> onclick="window.location.href='booking.php?id=<?= h($pkg['package_id']) ?>'">
                                <?= ($pkg['availability'] ?? 'available') === 'available' ? 'Book Now' : 'Unavailable' ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
    <script src="js/script.js"></script>
</body>

</html>
