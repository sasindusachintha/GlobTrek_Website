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

define('IN_SITE', true);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();
$user = currentUser();
$role = userRole();
$activePage = 'dashboard';
$section = trim($_GET['section'] ?? '');
$task = trim($_GET['task'] ?? '');
$actionMessage = '';

$menuByRole = [
    'user' => [
        'companyProfile' => 'Company Profile',
        'tourPackages' => 'Tour Packages',
        'profile' => 'My Profile',
        'bookings' => 'My Bookings',
        'customize' => 'Customize Travel Plan',
        'queries' => 'Queries',
        'payments' => 'My Payments',
    ],
    'staff' => [
        'companyProfile' => 'Company Profile',
        'tourPackages' => 'Tour Packages',
        'managePackages' => 'Manage Packages',
        'confirmBookings' => 'Confirm Bookings',
        'hotels' => 'Hotels Coordination',
        'transport' => 'Transport Providers',
        'customerQueries' => 'Customer Queries',
    ],
    'admin' => [
        'companyProfile' => 'Company Profile',
        'tourPackages' => 'Tour Packages',
        'manageUsers' => 'Manage Users',
        'confirmStaff' => 'Confirm Staff',
        'overseeBooking' => 'Oversee Booking',
        'reports' => 'Generate Reports',
    ],
];

if (!array_key_exists($section, $menuByRole[$role])) {
    $section = array_key_first($menuByRole[$role]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim($_POST['action'] ?? '');

    if ($action === 'cancel_booking') {
        $bookingId = intval($_POST['booking_id'] ?? 0);
        $booking = fetchBookingById($bookingId);
        if ($booking && intval($booking['user_id']) === intval($user['user_id'])) {
            updateBookingStatus($bookingId, 'cancelled');
            $actionMessage = 'Booking cancelled successfully.';
        }
    }

    if ($action === 'confirm_booking' && ($role === 'staff' || $role === 'admin')) {
        $bookingId = intval($_POST['booking_id'] ?? 0);
        updateBookingStatus($bookingId, 'confirmed');
        $actionMessage = 'Booking confirmed.';
    }

    if ($action === 'toggle_user' && $role === 'admin') {
        $userId = intval($_POST['user_id'] ?? 0);
        toggleUserApproval($userId);
        $actionMessage = 'User approval status updated.';
    }

    if ($action === 'toggle_staff' && $role === 'admin') {
        $userId = intval($_POST['user_id'] ?? 0);
        toggleStaffApproval($userId);
        $actionMessage = 'Staff approval status updated.';
    }

    if ($action === 'reset_approvals' && $role === 'admin') {
        query('UPDATE users SET status = 0 WHERE role = ?', ['staff']);
        $actionMessage = 'All staff approvals reset.';
    }

    if ($action === 'reply_query' && $role === 'staff') {
        $queryId = intval($_POST['query_id'] ?? 0);
        $response = trim($_POST['queryResponse'] ?? '');
        if ($response !== '') {
            query('UPDATE queries SET message = CONCAT(message, "\n\nResponse: ", ?), status = ? WHERE query_id = ?', [$response, 'responded', $queryId]);
            $actionMessage = 'Reply sent to customer query.';
        }
    }

    if ($action === 'submit_query' && $role === 'user') {
        $subject = 'Dashboard Inquiry';
        $messageText = trim($_POST['dashboardQueryMessage'] ?? '');
        if ($messageText !== '') {
            createQuery($user['user_id'], $user['email'], $subject, $messageText);
            $actionMessage = 'Your query was submitted.';
        }
    }

    if ($action === 'submit_plan' && $role === 'user') {
        $destination = trim($_POST['customDestination'] ?? '');
        $dates = trim($_POST['customDates'] ?? '');
        $notes = trim($_POST['customNotes'] ?? '');
        if ($destination && $dates && $notes) {
            createQuery($user['user_id'], $user['email'], 'Custom Plan', "Destination: $destination\nDates: $dates\nNotes: $notes");
            $actionMessage = 'Custom travel request submitted successfully.';
        }
    }

    if (($action === 'create_package' || $action === 'update_package') && ($role === 'staff' || $role === 'admin')) {
        $packageData = [
            'title' => trim($_POST['packageTitle'] ?? ''),
            'location' => trim($_POST['packageLocation'] ?? ''),
            'description' => trim($_POST['packageDescription'] ?? ''),
            'price' => floatval($_POST['packagePrice'] ?? 0),
            'image' => trim($_POST['packageImage'] ?? ''),
            'category' => trim($_POST['packageCategory'] ?? 'attractions'),
            'availability' => trim($_POST['packageAvailability'] ?? 'available'),
            'duration' => trim($_POST['packageDuration'] ?? ''),
            'transport' => trim($_POST['packageTransport'] ?? ''),
            'rating' => trim($_POST['packageRating'] ?? ''),
            'highlights' => trim($_POST['packageHighlights'] ?? ''),
            'itinerary' => trim($_POST['packageItinerary'] ?? ''),
            'included' => trim($_POST['packageIncluded'] ?? ''),
        ];
        if ($action === 'create_package') {
            createPackage($packageData);
            $actionMessage = 'Package added successfully.';
            $task = '';
        } else {
            $packageId = intval($_POST['package_id'] ?? 0);
            updatePackage($packageId, $packageData);
            $actionMessage = 'Package updated successfully.';
            $task = '';
        }
    }
}

if ($section === 'managePackages' && $task === 'add') {
    $contentTask = 'add';
} elseif ($section === 'managePackages' && $task === 'edit') {
    $contentTask = 'edit';
    $editPackageId = intval($_GET['id'] ?? 0);
    $editPackage = fetchPackageById($editPackageId);
    if (!$editPackage) {
        $contentTask = '';
    }
} else {
    $contentTask = '';
}

if ($role === 'staff' && !isset($menuByRole['staff'][$section])) {
    $section = array_key_first($menuByRole['staff']);
}
if ($role === 'admin' && !isset($menuByRole['admin'][$section])) {
    $section = array_key_first($menuByRole['admin']);
}
if ($role === 'user' && !isset($menuByRole['user'][$section])) {
    $section = array_key_first($menuByRole['user']);
}

$bookings = fetchAllBookings();
$packages = fetchAllPackages();
$hotels = fetchHotelRecords();
$transports = fetchTransportRecords();
$queries = fetchAllQueries();
$users = fetchAllUsers();
$userBookings = fetchBookingsByUserId($user['user_id']);
$userQueries = fetchUserQueries($user['email']);
$userPayments = fetchPaymentsByUserId($user['user_id']);
// Staff pagination settings.
$staffLimit = 5;
$totalStaffUsers = countStaffUsers();
$staffTotalPages = max(1, ceil($totalStaffUsers / $staffLimit));
$staffPage = max(1, intval($_GET['page'] ?? 1));
$staffPage = min($staffPage, $staffTotalPages);
$staffOffset = ($staffPage - 1) * $staffLimit;
$staffUsers = fetchStaffUsers($staffLimit, $staffOffset);
$hasNextStaffPage = ($staffOffset + $staffLimit) < $totalStaffUsers;
$totalUsers = countTotalUsers();
$totalBookings = countTotalBookings();
$totalRevenue = sumTotalRevenue();
$replyQuery = null;

if ($section === 'customerQueries' && $task === 'reply' && ($role === 'staff' || $role === 'admin')) {
    $replyQuery = fetchQueryById(intval($_GET['id'] ?? 0));
}

$download = trim($_GET['download'] ?? '');
if ($download === 'queries' && ($role === 'staff' || $role === 'admin')) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="customer-queries.csv"');
    echo "Name,Email,Message,Status\n";
    foreach ($queries as $q) {
        echo '"' . str_replace('"', '""', $q['full_name'] ?? '') . '",';
        echo '"' . str_replace('"', '""', $q['email']) . '",';
        echo '"' . str_replace('"', '""', $q['message']) . '",';
        echo '"' . str_replace('"', '""', $q['status']) . '"\n';
    }
    exit;
}

if ($download === 'report' && $role === 'admin') {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="globetrek-report-' . time() . '.json"');
    echo json_encode([
        'generatedAt' => date('c'),
        'totalBookings' => $totalBookings,
        'totalUsers' => $totalUsers,
        'totalRevenue' => $totalRevenue,
        'bookings' => $bookings,
        'users' => array_map(fn($row) => array_diff_key($row, ['password' => '']), $users),
    ], JSON_PRETTY_PRINT);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | GlobeTrek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main class="page-layout">
        <div class="container py-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h1 id="dashboardTitle" class="h3 mb-1"><?= h(ucfirst($role)) ?> Dashboard</h1>
                    <p id="dashboardRole" class="text-muted">Hello <?= h($user['full_name']) ?>, role: <?= h($role) ?></p>
                </div>
            </div>

            <?php if ($actionMessage): ?>
                <div class="alert alert-success"><?= h($actionMessage) ?></div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-3">
                    <div class="card shadow-sm rounded-4 p-3">
                        <h5 class="mb-3">Dashboard Menu</h5>
                        <div id="dashboardMenu" class="list-group list-group-flush">
                            <?php foreach ($menuByRole[$role] as $key => $label): ?>
                                <button type="button" class="list-group-item list-group-item-action <?= $section === $key ? 'active' : '' ?>" onclick="window.location.href='dashboard.php?section=<?= h($key) ?>'">
                                    <?= h($label) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div id="dashboardContent" class="card shadow-sm rounded-4 p-4">
                        <?php if ($section === 'companyProfile'): ?>
                            <h4>GlobeTrek Company Profile</h4>
                            <p>GlobeTrek Adventures is a full-service travel platform offering curated tour packages, accommodation options, transportation services, and expert travel guides.</p>
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item"><strong>Destinations:</strong> Global beach escapes, city breaks, mountain adventures, cultural tours.</li>
                                <li class="list-group-item"><strong>Accommodation:</strong> Resorts, boutique hotels, chalets, villas, and beachfront stays.</li>
                                <li class="list-group-item"><strong>Transportation:</strong> Private transfers, trains, boats, buses, and airport shuttles.</li>
                                <li class="list-group-item"><strong>Guides:</strong> Local experts, cultural specialists, and multilingual travel hosts.</li>
                            </ul>
                            <h5>Platform Capabilities</h5>
                            <p>Customers can browse packages, book trips, customize travel plans, make secure payments, and submit queries. Staff can update packages, confirm bookings, coordinate hotels and transport, and support customers. Admins manage staff, oversee booking operations, generate reports, and protect user data.</p>
                        <?php elseif ($section === 'tourPackages'): ?>
                            <h4>Available Tour Packages</h4>
                            <div class="row g-3">
                                <?php foreach ($packages as $pkg): ?>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card h-100 shadow-sm border-0">
                                            <img src="<?= h(assetPath($pkg['image'] ?? '', 'assets/images/hero1.jpg')) ?>" class="card-img-top" alt="<?= h($pkg['title']) ?>">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= h($pkg['title']) ?></h5>
                                                <p class="text-muted small mb-2"><i class="fa-solid fa-location-dot"></i> <?= h($pkg['location'] ?? '') ?></p>
                                                <p class="card-text text-muted mb-2"><?= h($pkg['description']) ?></p>
                                                <p class="mb-1"><strong>Duration:</strong> <?= h($pkg['duration'] ?? '') ?></p>
                                                <p class="mb-1"><strong>Transport:</strong> <?= h($pkg['transport'] ?? '') ?></p>
                                                <p class="mb-1"><strong>Price:</strong> LKR <?= number_format($pkg['price'], 0, '.', ',') ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif ($section === 'profile'): ?>
                            <h4>My Profile</h4>
                            <p><strong>Name:</strong> <?= h($user['full_name']) ?></p>
                            <p><strong>Email:</strong> <?= h($user['email']) ?></p>
                            <p><strong>Role:</strong> <?= h($role) ?></p>
                            <p><strong>Status:</strong> <?= intval($user['status']) === 1 ? 'Active' : 'Pending approval' ?></p>
                        <?php elseif ($section === 'bookings'): ?>
                            <h4>My Bookings</h4>
                            <div class="mb-3 d-flex gap-2">
                                <button class="btn btn-primary btn-sm" type="button" onclick="window.location.href='packages.php'">New Booking</button>
                            </div>
                            <?php if (count($userBookings)): ?>
                                <div class="table-responsive dashboard-table-wrapper">
                                    <table class="table table-borderless table-hover align-middle dashboard-table">
                                        <thead>
                                            <tr>
                                                <th>Package</th>
                                                <th>Travel Date</th>
                                                <th>Guests</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($userBookings as $bookingRow): ?>
                                                <tr>
                                                    <td><?= h($bookingRow['package_title']) ?></td>
                                                    <td><?= h($bookingRow['travel_date']) ?> to <?= h($bookingRow['return_date'] ?? '') ?></td>
                                                    <td><?= h($bookingRow['guests']) ?></td>
                                                    <td><span class="badge <?= badgeClassForStatus($bookingRow['status']) ?>"><?= h(ucfirst($bookingRow['status'])) ?></span></td>
                                                    <td>
                                                        <?php if ($bookingRow['status'] !== 'cancelled'): ?>
                                                            <form method="post" style="display:inline-block;">
                                                                <input type="hidden" name="action" value="cancel_booking">
                                                                <input type="hidden" name="booking_id" value="<?= h($bookingRow['booking_id']) ?>">
                                                                <button class="btn btn-sm btn-outline-danger">Cancel</button>
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="text-muted">No action</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>No bookings found. <a href="packages.php">Browse packages</a></p>
                            <?php endif; ?>
                        <?php elseif ($section === 'customize'): ?>
                            <h4>Customize Travel Plan</h4>
                            <p>Create a custom travel request for our team to review.</p>
                            <form method="post">
                                <input type="hidden" name="action" value="submit_plan">
                                <div class="mb-3"><label class="form-label">Desired destination</label><input class="form-control" name="customDestination" required></div>
                                <div class="mb-3"><label class="form-label">Preferred dates</label><input class="form-control" name="customDates" placeholder="e.g. June 10 - 15" required></div>
                                <div class="mb-3"><label class="form-label">Special requests</label><textarea class="form-control" name="customNotes" rows="4" required></textarea></div>
                                <button class="btn btn-primary" type="submit">Submit request</button>
                            </form>
                        <?php elseif ($section === 'queries'): ?>
                            <h4>My Queries</h4>
                            <div class="mb-3">
                                <button class="btn btn-primary btn-sm" type="button" onclick="window.location.href='contact.php'">Submit Query</button>
                            </div>
                            <?php if (count($userQueries)): ?>
                                <div class="table-responsive dashboard-table-wrapper">
                                    <table class="table table-borderless table-hover align-middle dashboard-table">
                                        <thead>
                                            <tr>
                                                <th>Message</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($userQueries as $queryRow): ?>
                                                <tr>
                                                    <td><?= nl2br(h($queryRow['message'])) ?></td>
                                                    <td><span class="badge <?= badgeClassForStatus($queryRow['status']) ?>"><?= h(ucfirst($queryRow['status'])) ?></span></td>
                                                    <td><?= h($queryRow['created_at']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>No submitted queries. <a href="contact.php">Submit a query</a></p>
                            <?php endif; ?>
                        <?php elseif ($section === 'payments'): ?>
                            <h4>Payments</h4>
                            <?php if (count($userPayments)): ?>
                                <div class="table-responsive dashboard-table-wrapper">
                                    <table class="table table-borderless table-hover align-middle dashboard-table">
                                        <thead>
                                            <tr>
                                                <th>Booking</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Paid On</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($userPayments as $paymentRow): ?>
                                                <tr>
                                                    <td><?= h($paymentRow['booking_id']) ?></td>
                                                    <td>LKR <?= number_format($paymentRow['amount'], 0, '.', ',') ?></td>
                                                    <td><span class="badge <?= badgeClassForStatus($paymentRow['payment_status']) ?>"><?= h(ucfirst($paymentRow['payment_status'])) ?></span></td>
                                                    <td><?= h($paymentRow['payment_date']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>No payments recorded.</p>
                            <?php endif; ?>
                        <?php elseif ($section === 'managePackages'): ?>
                            <h4>Manage Packages</h4>
                            <div class="mb-3 d-flex gap-2">
                                <button class="btn btn-primary btn-sm" type="button" onclick="window.location.href='dashboard.php?section=managePackages&task=add'">Add Package</button>
                            </div>
                            <?php if ($contentTask === 'add' || ($contentTask === 'edit' && isset($editPackage))): ?>
                                <?php $formPackage = $contentTask === 'edit' ? $editPackage : null; ?>
                                <form method="post">
                                    <input type="hidden" name="action" value="<?= $contentTask === 'edit' ? 'update_package' : 'create_package' ?>">
                                    <?php if ($contentTask === 'edit'): ?>
                                        <input type="hidden" name="package_id" value="<?= h($formPackage['package_id']) ?>">
                                    <?php endif; ?>
                                    <div class="row g-3">
                                        <div class="col-md-6"><label class="form-label">Title</label><input class="form-control" name="packageTitle" value="<?= h($formPackage['title'] ?? '') ?>" required></div>
                                        <div class="col-md-6"><label class="form-label">Location</label><input class="form-control" name="packageLocation" value="<?= h($formPackage['location'] ?? '') ?>"></div>
                                        <div class="col-md-6"><label class="form-label">Price</label><input type="number" step="0.01" class="form-control" name="packagePrice" value="<?= h($formPackage['price'] ?? '') ?>" required></div>
                                        <div class="col-md-6"><label class="form-label">Category</label><select class="form-select" name="packageCategory"><option value="attractions" <?= ($formPackage['category'] ?? '') === 'attractions' ? 'selected' : '' ?>>Attractions</option><option value="hotels" <?= ($formPackage['category'] ?? '') === 'hotels' ? 'selected' : '' ?>>Hotels</option><option value="restaurants" <?= ($formPackage['category'] ?? '') === 'restaurants' ? 'selected' : '' ?>>Restaurants</option></select></div>
                                        <div class="col-md-6"><label class="form-label">Availability</label><select class="form-select" name="packageAvailability"><option value="available" <?= ($formPackage['availability'] ?? '') === 'available' ? 'selected' : '' ?>>Available</option><option value="unavailable" <?= ($formPackage['availability'] ?? '') === 'unavailable' ? 'selected' : '' ?>>Unavailable</option></select></div>
                                        <div class="col-md-6"><label class="form-label">Main image path</label><input class="form-control" name="packageImage" value="<?= h($formPackage['image'] ?? '') ?>"></div>
                                        <div class="col-md-6"><label class="form-label">Duration</label><input class="form-control" name="packageDuration" value="<?= h($formPackage['duration'] ?? '') ?>"></div>
                                        <div class="col-md-6"><label class="form-label">Transport</label><input class="form-control" name="packageTransport" value="<?= h($formPackage['transport'] ?? '') ?>"></div>
                                        <div class="col-md-6"><label class="form-label">Rating</label><input class="form-control" name="packageRating" value="<?= h($formPackage['rating'] ?? '') ?>"></div>
                                        <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="packageDescription" rows="3"><?= h($formPackage['description'] ?? '') ?></textarea></div>
                                        <div class="col-12"><label class="form-label">Highlights (one per line)</label><textarea class="form-control" name="packageHighlights" rows="3"><?= h($formPackage['highlights'] ?? '') ?></textarea></div>
                                        <div class="col-12"><label class="form-label">Itinerary (day|activity per line)</label><textarea class="form-control" name="packageItinerary" rows="3"><?= h($formPackage['itinerary'] ?? '') ?></textarea></div>
                                        <div class="col-12"><label class="form-label">Included (one per line)</label><textarea class="form-control" name="packageIncluded" rows="3"><?= h($formPackage['included'] ?? '') ?></textarea></div>
                                    </div>
                                    <button class="btn btn-success mt-3"><?= $contentTask === 'edit' ? 'Save Changes' : 'Add Package' ?></button>
                                    <button type="button" class="btn btn-link mt-3" onclick="window.location.href='dashboard.php?section=managePackages'">Back to list</button>
                                </form>
                            <?php else: ?>
                                <?php if (count($packages)): ?>
                                    <div class="table-responsive dashboard-table-wrapper">
                                        <table class="table table-borderless table-hover align-middle dashboard-table">
                                            <thead>
                                                <tr><th>Name</th><th>Location</th><th>Duration</th><th>Price</th><th>Availability</th><th>Action</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($packages as $pkg): ?>
                                                    <tr>
                                                        <td><?= h($pkg['title']) ?></td>
                                                        <td><?= h($pkg['location'] ?? '') ?></td>
                                                        <td><?= h($pkg['duration'] ?? '') ?></td>
                                                        <td>LKR <?= number_format($pkg['price'], 0, '.', ',') ?></td>
                                                        <td><span class="badge <?= $pkg['availability'] === 'available' ? 'bg-success' : 'bg-secondary' ?>"><?= h(ucfirst($pkg['availability'])) ?></span></td>
                                                        <td><button class="btn btn-sm btn-outline-primary" type="button" onclick="window.location.href='dashboard.php?section=managePackages&task=edit&id=<?= h($pkg['package_id']) ?>'">Edit</button></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p>No packages available.</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php elseif ($section === 'confirmBookings'): ?>
                            <h4>Confirm Bookings</h4>
                            <?php if (count($bookings)): ?>
                                <div class="table-responsive dashboard-table-wrapper">
                                    <table class="table table-borderless table-hover align-middle dashboard-table">
                                        <thead>
                                            <tr><th>Package</th><th>Customer</th><th>Travel Date</th><th>Status</th><th>Action</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($bookings as $bookingRow): ?>
                                                <tr>
                                                    <td><?= h($bookingRow['package_title']) ?></td>
                                                    <td><?= h($bookingRow['customer_name']) ?></td>
                                                    <td><?= h($bookingRow['travel_date']) ?></td>
                                                    <td><span class="badge <?= badgeClassForStatus($bookingRow['status']) ?>"><?= h(ucfirst($bookingRow['status'])) ?></span></td>
                                                    <td>
                                                        <form method="post" style="display:inline-block;">
                                                            <input type="hidden" name="action" value="confirm_booking">
                                                            <input type="hidden" name="booking_id" value="<?= h($bookingRow['booking_id']) ?>">
                                                            <button class="btn btn-sm btn-outline-success" <?= in_array($bookingRow['status'], ['confirmed', 'cancelled']) ? 'disabled' : '' ?>>Confirm</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>No bookings available.</p>
                            <?php endif; ?>
                        <?php elseif ($section === 'hotels'): ?>
                            <h4>Hotels Coordination</h4>
                            <?php if (count($hotels)): ?>
                                <div class="list-group">
                                    <?php foreach ($hotels as $hotel): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?= h($hotel['name']) ?></strong>
                                                <div class="text-muted"><?= h($hotel['location']) ?></div>
                                            </div>
                                            <span class="badge <?= $hotel['status'] === 'confirmed' ? 'bg-success' : 'bg-warning text-dark' ?>"><?= h(ucfirst($hotel['status'])) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p>No hotel records found.</p>
                            <?php endif; ?>
                        <?php elseif ($section === 'transport'): ?>
                            <h4>Transport Providers</h4>
                            <?php if (count($transports)): ?>
                                <div class="list-group">
                                    <?php foreach ($transports as $transportRow): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div><?= h($transportRow['type']) ?></div>
                                            <span class="badge <?= $transportRow['status'] === 'ready' ? 'bg-success' : ($transportRow['status'] === 'confirmed' ? 'bg-primary' : 'bg-info text-dark') ?>"><?= h(ucfirst($transportRow['status'])) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p>No transport data available.</p>
                            <?php endif; ?>
                        <?php elseif ($section === 'customerQueries'): ?>
                            <h4>Customer Queries</h4>
                            <div class="mb-3 d-flex gap-2">
                                <a class="btn btn-secondary btn-sm" href="dashboard.php?section=customerQueries&download=queries">Export Queries</a>
                            </div>
                            <?php if ($replyQuery): ?>
                                <form method="post" class="mb-4">
                                    <input type="hidden" name="action" value="reply_query">
                                    <input type="hidden" name="query_id" value="<?= h($replyQuery['query_id']) ?>">
                                    <div class="mb-2">
                                        <strong><?= h($replyQuery['full_name'] ?? 'Guest') ?></strong>
                                        <div class="text-muted"><?= h($replyQuery['email']) ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Query</label>
                                        <div class="form-control bg-light" style="min-height: 90px;"><?= nl2br(h($replyQuery['message'])) ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Reply</label>
                                        <textarea class="form-control" name="queryResponse" rows="4" required></textarea>
                                    </div>
                                    <button class="btn btn-primary btn-sm" type="submit">Send Reply</button>
                                    <button class="btn btn-link btn-sm" type="button" onclick="window.location.href='dashboard.php?section=customerQueries'">Cancel</button>
                                </form>
                            <?php endif; ?>
                            <?php if (count($queries)): ?>
                                <div class="table-responsive dashboard-table-wrapper">
                                    <table class="table table-borderless table-hover align-middle dashboard-table">
                                        <thead>
                                            <tr><th>Name</th><th>Email</th><th>Query</th><th>Status</th><th>Action</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($queries as $queryRow): ?>
                                                <tr>
                                                    <td><?= h($queryRow['full_name'] ?? 'Guest') ?></td>
                                                    <td><?= h($queryRow['email']) ?></td>
                                                    <td><?= nl2br(h($queryRow['message'])) ?></td>
                                                    <td><span class="badge <?= badgeClassForStatus($queryRow['status']) ?>"><?= h(ucfirst($queryRow['status'])) ?></span></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" type="button" onclick="window.location.href='dashboard.php?section=customerQueries&task=reply&id=<?= h($queryRow['query_id']) ?>'">Reply</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>No queries found.</p>
                            <?php endif; ?>
                        <?php elseif ($section === 'manageUsers'): ?>
                            <h4>Manage Users</h4>
                            <?php if (count($users)): ?>
                                <div class="table-responsive dashboard-table-wrapper">
                                    <table class="table table-borderless table-hover align-middle dashboard-table">
                                        <thead>
                                            <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Action</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users as $userRow): ?>
                                                <tr>
                                                    <td><?= h($userRow['full_name']) ?></td>
                                                    <td><?= h($userRow['email']) ?></td>
                                                    <td><?= h($userRow['role']) ?></td>
                                                    <td><span class="badge <?= intval($userRow['status']) === 1 ? 'bg-success' : 'bg-warning text-dark' ?>"><?= intval($userRow['status']) === 1 ? 'Active' : 'Pending' ?></span></td>
                                                    <td>
                                                        <form method="post" style="display:inline-block;">
                                                            <input type="hidden" name="action" value="toggle_user">
                                                            <input type="hidden" name="user_id" value="<?= h($userRow['user_id']) ?>">
                                                            <button class="btn btn-sm btn-outline-secondary"><?= intval($userRow['status']) === 1 ? 'Deactivate' : 'Approve' ?></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>No user accounts found.</p>
                            <?php endif; ?>
                        <?php elseif ($section === 'confirmStaff'): ?>
                            <h4>Confirm Staff</h4>
                            <div class="mb-3 d-flex gap-2">
                                <form method="post" style="display:inline-block;"><input type="hidden" name="action" value="reset_approvals"><button class="btn btn-danger btn-sm">Reset Approvals</button></form>
                            </div>
                            <?php if (count($staffUsers)): ?>
                                <div class="table-responsive dashboard-table-wrapper">
                                    <table class="table table-borderless table-hover align-middle dashboard-table">
                                        <thead>
                                            <tr><th>Name</th><th>Email</th><th>Status</th><th>Action</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($staffUsers as $staffRow): ?>
                                                <tr>
                                                    <td><?= h($staffRow['full_name']) ?></td>
                                                    <td><?= h($staffRow['email']) ?></td>
                                                    <td><?= intval($staffRow['status']) === 1 ? 'Approved' : 'Pending' ?></td>
                                                    <td>
                                                        <form method="post" style="display:inline-block;">
                                                            <input type="hidden" name="action" value="toggle_staff">
                                                            <input type="hidden" name="user_id" value="<?= h($staffRow['user_id']) ?>">
                                                            <button class="btn btn-sm btn-outline-secondary"><?= intval($staffRow['status']) === 1 ? 'Revoke' : 'Approve' ?></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <!-- Previous is hidden on page 1. Next is hidden on the last page. -->
                                    <?php if ($staffPage > 1): ?>
                                        <a class="btn btn-outline-secondary btn-sm" href="dashboard.php?section=confirmStaff&page=<?= h($staffPage - 1) ?>">Previous</a>
                                    <?php endif; ?>

                                    <?php if ($hasNextStaffPage): ?>
                                        <a class="btn btn-outline-primary btn-sm" href="dashboard.php?section=confirmStaff&page=<?= h($staffPage + 1) ?>">Next</a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <p>No staff accounts yet.</p>
                            <?php endif; ?>
                        <?php elseif ($section === 'overseeBooking'): ?>
                            <h4>Oversee Booking</h4>
                            <?php if (count($bookings)): ?>
                                <div class="table-responsive dashboard-table-wrapper">
                                    <table class="table table-borderless table-hover align-middle dashboard-table">
                                        <thead>
                                            <tr><th>Package</th><th>Customer</th><th>Travel Date</th><th>Status</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($bookings as $bookingRow): ?>
                                                <tr>
                                                    <td><?= h($bookingRow['package_title']) ?></td>
                                                    <td><?= h($bookingRow['customer_name']) ?></td>
                                                    <td><?= h($bookingRow['travel_date']) ?></td>
                                                    <td><?= h($bookingRow['status']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>No bookings available.</p>
                            <?php endif; ?>
                        <?php elseif ($section === 'reports'): ?>
                            <h4>Reports & Analytics</h4>
                            <div class="mb-3 d-flex gap-2">
                                <a class="btn btn-success btn-sm" href="dashboard.php?section=reports&download=report">Download Report</a>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4"><div class="p-4 bg-light rounded"><h5><?= h($totalBookings) ?></h5><p>Total bookings</p></div></div>
                                <div class="col-md-4"><div class="p-4 bg-light rounded"><h5><?= h($totalUsers) ?></h5><p>Total users</p></div></div>
                                <div class="col-md-4"><div class="p-4 bg-light rounded"><h5>LKR <?= number_format($totalRevenue, 0, '.', ',') ?></h5><p>Total revenue</p></div></div>
                            </div>
                            <div class="alert alert-info"><strong>Report Summary:</strong> This dashboard shows key metrics. Generate detailed reports using the button above.</div>
                        <?php else: ?>
                            <h4>Welcome</h4>
                            <p>Select a section from the menu to view details.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
