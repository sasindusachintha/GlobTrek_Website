<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) && isset($_SESSION['user']['user_id'])) {
    $_SESSION['user_id'] = $_SESSION['user']['user_id'];
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

define('IN_SITE', true);
require_once __DIR__ . '/includes/functions.php';
requireLogin();
$packageId = isset($_POST['package_id']) ? intval($_POST['package_id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
$package = fetchPackageById($packageId);
if (!$package) {
    redirect('packages.php');
}
$user = currentUser();
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $travelDate = trim($_POST['travelDate'] ?? '');
    $returnDate = trim($_POST['returnDate'] ?? '');
    $guestCount = intval($_POST['guestCount'] ?? 0);
    $phone = trim($_POST['bookingPhone'] ?? '');

    if (!$travelDate || !$guestCount) {
        $errorMessage = 'Please complete the travel date and guest count.';
    } elseif ($returnDate && strtotime($returnDate) < strtotime($travelDate)) {
        $errorMessage = 'Return date cannot be before the travel date.';
    } elseif ($guestCount < 1) {
        $errorMessage = 'Guests must be at least 1.';
    } else {
        $baseTotal = $package['price'] * $guestCount;
        $taxes = round($baseTotal * 0.10);
        $serviceFee = 450;
        $totalPrice = $baseTotal + $taxes + $serviceFee;
        $bookingId = insertBooking($user['user_id'], $packageId, $travelDate, $returnDate, $guestCount, $phone, $totalPrice);
        redirect('payment.php?booking_id=' . intval($bookingId));
    }
}
$activePage = 'booking';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking | GlobeTrek</title>
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
          <div class="form-panel">
            <h3 class="mb-4">Booking Form</h3>
            <form method="post" id="bookingForm">
              <input type="hidden" name="package_id" value="<?= h($package['package_id']) ?>">
              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input id="bookingName" name="bookingName" type="text" class="form-control form-control-lg" autocomplete="name" placeholder="Enter your full name" value="<?= h($user['full_name']) ?>" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">Email address</label>
                <input id="bookingEmail" name="bookingEmail" type="email" class="form-control form-control-lg" autocomplete="email" placeholder="name@example.com" value="<?= h($user['email']) ?>" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">Phone number</label>
                <input id="bookingPhone" name="bookingPhone" type="tel" class="form-control form-control-lg" autocomplete="tel" placeholder="Enter phone number" value="<?= h($_POST['bookingPhone'] ?? '') ?>">
              </div>
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label">Travel Date</label>
                  <input id="travelDate" name="travelDate" type="date" class="form-control form-control-lg" value="<?= h($_POST['travelDate'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Return Date</label>
                  <input id="returnDate" name="returnDate" type="date" class="form-control form-control-lg" value="<?= h($_POST['returnDate'] ?? '') ?>">
                </div>
              </div>
              <div class="mb-4">
                <label class="form-label">Number of Guests</label>
                <input id="guestCount" name="guestCount" type="number" class="form-control form-control-lg" min="1" placeholder="2" value="<?= h($_POST['guestCount'] ?? 1) ?>">
              </div>
              <?php if ($errorMessage): ?>
                <div id="bookingMessage" class="mb-3 text-danger"><?= h($errorMessage) ?></div>
              <?php else: ?>
                <div id="bookingMessage" class="mb-3 text-danger"></div>
              <?php endif; ?>
              <button type="submit" class="btn btn-primary btn-lg">Continue to Payment</button>
            </form>
          </div>
        </div>
        <div class="col-xl-5">
          <div class="summary-panel">
            <div class="about-image mb-4">Package Preview</div>
            <div class="px-0 px-md-3 pb-4">
              <div class="summary-badge"><i class="fa-solid fa-map-pin"></i> Top package</div>
              <h4 id="packageName"><?= h($package['title']) ?></h4>
              <p id="packageDescription" class="text-muted"><?= h($package['description']) ?></p>
              <hr>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Duration</span>
                <strong id="packageDuration"><?= h($package['duration'] ?? '') ?></strong>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Transport</span>
                <strong id="packageTransport"><?= h($package['transport'] ?? '') ?></strong>
              </div>
              <div class="d-flex justify-content-between mb-4">
                <span class="text-muted">Price</span>
                <strong id="packagePrice">LKR <?= number_format($package['price'], 0, '.', ',') ?></strong>
              </div>
              <div class="text-muted">Confirm your details and proceed to secure payment on the next screen.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="assets/js/app.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
