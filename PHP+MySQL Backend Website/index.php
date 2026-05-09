<?php
define('IN_SITE', true);
require_once __DIR__ . '/functions.php';
$packages = fetchAllPackages();
$activePage = 'home';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlobeTrek Adventures | Travel Starts Here</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include __DIR__ . '/header.php'; ?>

    <main class="container text-center hero-section">
        <h1 class="hero-title">Travel Starts Here</h1>

        <ul class="nav nav-tabs-custom" id="categoryTabs">
            <li class="nav-item">
                <button class="nav-link active" data-category="all">
                    <i class="fa-solid fa-layer-group"></i> <span>Search All</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-category="hotels">
                    <i class="fa-solid fa-hotel"></i> <span>Hotels</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-category="restaurants">
                    <i class="fa-solid fa-utensils"></i> <span>Restaurants</span>
                </button>
            </li>
        </ul>

        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" class="search-bar" placeholder="Places to go, Hotels...">
            <button class="search-btn">
                Search
            </button>
        </div>

        <div class="hero-image-container">
            <img id="heroImage" src="images/hero1.jpg" alt="Travel Image">
        </div>
    </main>

    <div class="category-sticky-wrapper">
        <div class="container">
            <ul class="nav nav-tabs-custom" id="categoryTabsSticky">
                <li class="nav-item">
                    <button class="nav-link active" data-category="all">
                        <i class="fa-regular fa-square-check"></i> Search All
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-category="hotels">
                        <i class="fa-regular fa-square"></i> Hotels
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-category="restaurants">
                        <i class="fa-regular fa-square"></i> Restaurants
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <section class="container mt-5 pt-4">
        <h2 class="section-title text-start mb-4">Browse Packages</h2>
        <div class="row g-4" id="package-container">
            <?php foreach ($packages as $pkg): ?>
                <div class="col-lg-3 col-md-6" data-category="<?= h($pkg['category'] ?: 'all') ?>">
                    <div class="package-card shadow-sm border rounded-3 overflow-hidden bg-white h-100">
                        <a href="package-details.php?id=<?= h($pkg['package_id']) ?>" class="text-decoration-none text-dark">
                            <div class="package-img"
                                style="background-image:url('<?= h($pkg['image'] ?: 'images/hero1.jpg') ?>');height:200px;background-size:cover;background-position:center;"></div>
                            <div class="p-3">
                                <h3 class="h5 fw-bold mb-1"><?= h($pkg['title']) ?></h3>
                                <p class="text-muted small mb-1"><i class="fa-solid fa-location-dot"></i> <?= h($pkg['location'] ?? '') ?></p>
                                <p class="text-warning mb-1">⭐ <?= h($pkg['rating'] ?? '') ?></p>
                                <p class="fw-bold text-success mb-2">LKR <?= number_format($pkg['price'], 0, '.', ',') ?></p>
                                <span class="badge <?= $pkg['availability'] === 'available' ? 'bg-success' : 'bg-secondary' ?>"><?= $pkg['availability'] === 'available' ? 'Available' : 'Unavailable' ?></span>
                            </div>
                        </a>
                        <div class="p-3 pt-0 d-flex justify-content-between align-items-center">
                            <button class="btn btn-primary btn-sm rounded-pill" <?= $pkg['availability'] === 'available' ? '' : 'disabled' ?> onclick="window.location.href='booking.php?id=<?= h($pkg['package_id']) ?>'">
                                <?= $pkg['availability'] === 'available' ? 'Book Now' : 'Unavailable' ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="feedback-section">
        <div class="container">
            <div class="feedback-heading">
                <h2 class="section-title mb-2">Traveler Feedback</h2>
                <p>Real stories from guests who planned their holidays with GlobeTrek.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feedback-card">
                        <div class="feedback-rating">5/5</div>
                        <p>"The Bali package was smooth from airport pickup to the villa stay. Everything felt easy."</p>
                        <strong>Nethmi Perera</strong>
                        <span>Family traveler</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feedback-card feedback-card-large">
                        <div class="feedback-rating">5/5</div>
                        <p>"GlobeTrek helped us choose the right dates, hotel, and transport. The staff support made the trip feel very organized."</p>
                        <strong>Kasun Silva</strong>
                        <span>Honeymoon trip</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feedback-card">
                        <div class="feedback-rating">4/5</div>
                        <p>"Loved the Paris tour. Good price, friendly guide, and quick replies whenever we had a question."</p>
                        <strong>Amanda Fernando</strong>
                        <span>City explorer</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-us-section bg-light" id="about-us">
        <div class="container">
            <div class="row g-4 align-items-center about-us-card mx-auto">
                <div class="col-lg-6 about-image">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63370.20000000001!2d79.80000000000001!3d6.927079!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2590000000000%3A0x0000000000000000!2sNegombo%2C%20Sri%20Lanka!5e0!3m2!1sen!2slk!4v0000000000000"
                        width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <div class="col-lg-6 about-content">
                    <h3>About Us</h3>
                    <p>GlobeTrek Adventures brings curated travel experiences to life. We combine local expertise,
                        premium service, and seamless planning so every journey becomes unforgettable.</p>
                    <p>From luxury escapes to cultural city breaks, our team tailors each itinerary to your interests,
                        comfort, and budget. Enjoy trusted accommodations, expert guides, and round-the-clock support
                        while you travel.</p>
                    <p>We believe travel should feel effortless and inspiring. Let GlobeTrek guide your next adventure
                        with clarity, confidence, and a touch of luxury.</p>
                    <div class="about-contact">
                        <p><i class="fa-solid fa-envelope"></i> hello@globetrek-adventures.test</p>
                        <p><i class="fa-solid fa-phone"></i> +94 77 456 8890</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
    <script src="js/script.js"></script>
</body>

</html>
