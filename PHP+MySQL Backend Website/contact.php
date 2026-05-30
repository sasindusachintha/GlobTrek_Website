<?php
define('IN_SITE', true);
require_once __DIR__ . '/includes/functions.php';
$user = currentUser();
$errorMessage = '';

$successMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['queryName'] ?? '');
    $email = trim($_POST['queryEmail'] ?? '');
    $message = trim($_POST['queryMessage'] ?? '');
    $userId = $user['user_id'] ?? null;

    if (!$name || !$email || !$message) {
        $errorMessage = 'Please complete all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } elseif (strlen($message) < 10) {
        $errorMessage = 'Please enter a message with at least 10 characters.';
    } else {
        createQuery($userId, $email, 'Contact Inquiry', $message);
        $successMessage = 'Your query has been submitted. We will respond shortly.';
    }
}
$activePage = 'contact';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | GlobeTrek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main class="page-layout query-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="query-panel">
                        <h3 class="mb-4 text-center">Submit Your Query</h3>
                        <form id="queryForm" method="post">
                            <div class="mb-3">
                                <input id="queryName" name="queryName" type="text" class="form-control form-control-lg" placeholder="Name" value="<?= h($_POST['queryName'] ?? $user['full_name'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <input id="queryEmail" name="queryEmail" type="email" class="form-control form-control-lg" placeholder="Email" value="<?= h($_POST['queryEmail'] ?? $user['email'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <textarea id="queryMessage" name="queryMessage" class="form-control form-control-lg" rows="6" placeholder="Message"><?= h($_POST['queryMessage'] ?? '') ?></textarea>
                            </div>
                            <?php if ($errorMessage): ?>
                                <div id="queryMessageArea" class="mb-3 text-danger"><?= h($errorMessage) ?></div>
                            <?php elseif ($successMessage): ?>
                                <div id="queryMessageArea" class="mb-3 text-success"><?= h($successMessage) ?></div>
                            <?php else: ?>
                                <div id="queryMessageArea" class="mb-3 text-danger"></div>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary btn-lg w-100">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <section class="feedback-section">
        <div class="container">
            <div class="feedback-heading">
                <h2 class="section-title mb-2">Traveler Feedback</h2>
                <p>Helpful notes from travelers who booked with GlobeTrek.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feedback-card">
                        <div class="feedback-rating">5/5</div>
                        <p>"The team answered every question quickly and helped us choose the best package."</p>
                        <strong>Dinuka Jayasuriya</strong>
                        <span>Solo traveler</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feedback-card feedback-card-large">
                        <div class="feedback-rating">5/5</div>
                        <p>"Booking was simple, payment was clear, and the staff confirmation gave us extra confidence before the trip."</p>
                        <strong>Sarah Wijesinghe</strong>
                        <span>Couple getaway</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feedback-card">
                        <div class="feedback-rating">4/5</div>
                        <p>"Very friendly service. The hotel and transport details were well coordinated."</p>
                        <strong>Ravi Kumar</strong>
                        <span>Business traveler</span>
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
                    <p>GlobeTrek Adventures brings curated travel experiences to life with local expertise, premium service, and seamless planning.</p>
                    <p>From luxury escapes to cultural city breaks, our team tailors each itinerary to your interests, comfort, and budget.</p>
                    <p>We believe travel should feel effortless and inspiring. Let GlobeTrek guide your next adventure with clarity and confidence.</p>
                    <div class="about-contact">
                        <p><i class="fa-solid fa-envelope"></i> hello@globetrek-adventures@gmail.com</p>
                        <p><i class="fa-solid fa-phone"></i> +94 77 456 8890</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="assets/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
