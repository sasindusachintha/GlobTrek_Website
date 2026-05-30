<?php
define('IN_SITE', true);
require_once __DIR__ . '/includes/functions.php';
requireLogin();
$bookingId = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
$booking = fetchBookingById($bookingId);
if (!$booking) {
    redirect('dashboard.php');
}
$user = currentUser();
if ($user['user_id'] !== $booking['user_id'] && userRole() === 'user') {
    redirect('dashboard.php');
}
$errorMessage = '';
success:
$paymentSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cardNumber = preg_replace('/\s+/', '', trim($_POST['cardNumber'] ?? ''));
    $cardHolder = trim($_POST['cardHolder'] ?? '');
    $cardExpiry = trim($_POST['cardExpiry'] ?? '');
    $cardCvv = trim($_POST['cardCvv'] ?? '');

    if (!preg_match('/^\d{13,19}$/', $cardNumber)) {
        $errorMessage = 'Please enter a valid card number.';
    } elseif (strlen($cardHolder) < 3) {
        $errorMessage = 'Please enter the card holder name.';
    } elseif (!$cardExpiry) {
        $errorMessage = 'Please select the card expiration date.';
    } elseif (!preg_match('/^\d{4}-\d{2}$/', $cardExpiry)) {
        $errorMessage = 'Please enter a valid expiration date.';
    } elseif (!preg_match('/^\d{3,4}$/', $cardCvv)) {
        $errorMessage = 'Please enter a valid CVV.';
    } else {
        $paymentAmount = $booking['total_price'];
        insertPayment($bookingId, $paymentAmount, 'card', 'paid');
        updateBookingStatus($bookingId, 'confirmed');
        $paymentSuccess = true;
    }
}
$activePage = 'payment';
$packagePrice = $booking['package_price'] * $booking['guests'];
$taxes = round($packagePrice * 0.10);
$serviceFee = 450;
$totalValue = $packagePrice + $taxes + $serviceFee;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment | GlobeTrek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main class="page-layout">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-xl-7">
                    <div class="payment-panel">
                        <h3 class="mb-4">Confirm and Pay</h3>
                        <?php if ($paymentSuccess): ?>
                            <div class="alert alert-success">Payment successful. Your booking is confirmed.</div>
                        <?php endif; ?>
                        <form id="paymentForm" method="post" <?= $paymentSuccess ? 'style="display:none;"' : '' ?>>
                            <div class="mb-3">
                                <label class="form-label">Card number</label>
                                <input id="cardNumber" name="cardNumber" type="text" class="form-control form-control-lg" inputmode="numeric" autocomplete="cc-number" placeholder="1234 5678 9012 3456">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Card holder</label>
                                <input id="cardHolder" name="cardHolder" type="text" class="form-control form-control-lg" autocomplete="cc-name" placeholder="Full name on card">
                            </div>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Expiration date</label>
                                    <input id="cardExpiry" name="cardExpiry" type="month" class="form-control form-control-lg" autocomplete="cc-exp">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">CVV</label>
                                    <input id="cardCvv" name="cardCvv" type="text" class="form-control form-control-lg" inputmode="numeric" autocomplete="cc-csc" placeholder="123">
                                </div>
                            </div>
                            <?php if ($errorMessage): ?>
                                <div id="paymentMessage" class="mt-3 text-danger"><?= h($errorMessage) ?></div>
                            <?php else: ?>
                                <div id="paymentMessage" class="mt-3 text-danger"></div>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary btn-lg mt-4 w-100">Confirm and pay</button>
                        </form>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="summary-panel mb-4">
                        <div class="payment-package-card">
                            <div class="package-thumb">
                                <img src="<?= h(assetPath($booking['package_image'] ?? '', 'assets/images/hero1.jpg')) ?>" alt="Package preview">
                            </div>
                            <div class="package-summary-text">
                                <h5 id="paymentPackageName"><?= h($booking['package_title']) ?></h5>
                                <p id="paymentPackageSummary" class="text-muted mb-3"><?= h($booking['travel_date']) ?> to <?= h($booking['return_date'] ?? '') ?> · <?= h($booking['guests']) ?> travelers</p>
                                <div class="package-meta">
                                    <span><i class="fa-solid fa-calendar-days"></i> <?= h($booking['travel_date']) ?> - <?= h($booking['return_date'] ?? '') ?></span>
                                    <span><i class="fa-solid fa-users"></i> <?= h($booking['guests']) ?> travelers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="payment-details">
                        <h5>Price Details</h5>
                        <div class="detail-row"><span>Package price</span><span>LKR <?= number_format($packagePrice, 0, '.', ',') ?></span></div>
                        <div class="detail-row"><span>Taxes & fees</span><span>LKR <?= number_format($taxes, 0, '.', ',') ?></span></div>
                        <div class="detail-row"><span>Service fee</span><span>LKR <?= number_format($serviceFee, 0, '.', ',') ?></span></div>
                        <div class="detail-row total-row"><span>Total</span><span id="paymentTotal">LKR <?= number_format($totalValue, 0, '.', ',') ?></span></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
