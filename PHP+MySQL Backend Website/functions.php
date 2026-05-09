<?php
require_once __DIR__ . '/config/config.php';

function query($sql, array $params = []) {
    $conn = db();
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Database query error: ' . $conn->error);
    }
    if ($params) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}

function tableExists($table) {
    $stmt = query(
        'SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
        [$table]
    );
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

function packageTable() {
    return tableExists('tour_packages') ? 'tour_packages' : 'packages';
}

function firstExistingColumn($table, array $columns) {
    foreach ($columns as $column) {
        if (columnExists($table, $column)) {
            return $column;
        }
    }

    return $columns[0];
}

function packageIdColumn() {
    $table = packageTable();
    return firstExistingColumn($table, ['id', 'package_id']);
}

function packageTitleColumn() {
    $table = packageTable();
    return firstExistingColumn($table, ['title', 'name', 'package_name']);
}

function packageDescriptionColumn() {
    $table = packageTable();
    return firstExistingColumn($table, ['description', 'details']);
}

function packageImageColumn() {
    $table = packageTable();
    return firstExistingColumn($table, ['image', 'main_image', 'image_url']);
}

function packageSelectSql($tableAlias = '') {
    $table = packageTable();
    $prefix = $tableAlias ? "`$tableAlias`." : '';
    $id = packageIdColumn();
    $title = packageTitleColumn();
    $description = packageDescriptionColumn();
    $image = packageImageColumn();

    return "$prefix*, $prefix`$id` AS package_id, $prefix`$title` AS title, $prefix`$description` AS description, $prefix`$image` AS image";
}

function fetchAllPackages() {
    $table = packageTable();
    $id = packageIdColumn();
    $stmt = query("SELECT " . packageSelectSql() . " FROM `$table` ORDER BY `$id` ASC");
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchVisiblePackages() {
    $table = packageTable();
    $id = packageIdColumn();
    $stmt = query("SELECT " . packageSelectSql() . " FROM `$table` WHERE availability = ? ORDER BY `$id` ASC", ['available']);
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchPackageById($id) {
    $table = packageTable();
    $idColumn = packageIdColumn();
    $stmt = query("SELECT " . packageSelectSql() . " FROM `$table` WHERE `$idColumn` = ?", [$id]);
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function fetchPackageGallery($packageId, $limit = 4) {
    $limit = intval($limit);
    $imageColumn = columnExists('package_gallery', 'image_path') ? 'image_path' : firstExistingColumn('package_gallery', ['image', 'image_url']);
    $orderColumn = columnExists('package_gallery', 'gallery_id') ? 'gallery_id' : firstExistingColumn('package_gallery', ['id', 'package_id']);
    $stmt = query("SELECT *, `$imageColumn` AS image_path FROM package_gallery WHERE package_id = ? ORDER BY `$orderColumn` ASC LIMIT " . $limit, [$packageId]);
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchUserByEmail($email) {
    $stmt = query('SELECT * FROM users WHERE email = ?', [trim(strtolower($email))]);
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function fetchUserById($id) {
    $stmt = query('SELECT * FROM users WHERE user_id = ?', [$id]);
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function createUser($fullName, $email, $password, $role) {
    $role = normalizeRole($role);
    $status = $role === 'customer' ? 1 : 0;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    query(
        'INSERT INTO users (full_name, email, password, role, status) VALUES (?, ?, ?, ?, ?)',
        [$fullName, $email, $hashedPassword, $role, $status]
    );

    return db()->insert_id;
}

function loginUser($email, $password) {
    $user = fetchUserByEmail($email);
    if (!$user) {
        return null;
    }

    if (!password_verify($password, $user['password'])) {
        return null;
    }

    return $user;
}

function setAuthUser(array $user) {
    $user['role'] = normalizeRole($user['role']);
    $_SESSION['user'] = $user;
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['role'] = $user['role'];
}

function logoutUser() {
    session_unset();
    session_destroy();
}

function requireLogin() {
    if (!isLoggedIn()) {
        $redirectUrl = $_SERVER['REQUEST_URI'];
        redirect('login.php?redirect=' . urlencode($redirectUrl));
    }
}

function userRole() {
    $role = normalizeRole(currentUser()['role'] ?? 'customer');
    return $role === 'customer' ? 'user' : $role;
}

function badgeClassForStatus($status) {
    switch (strtolower($status)) {
        case 'confirmed':
            return 'bg-success';
        case 'cancelled':
            return 'bg-danger';
        case 'paid':
            return 'bg-success';
        case 'pending':
            return 'bg-warning text-dark';
        case 'responded':
            return 'bg-success';
        case 'open':
            return 'bg-info';
        default:
            return 'bg-secondary';
    }
}

function fetchBookingsByUserId($userId) {
    $packageTable = packageTable();
    $packageIdColumn = packageIdColumn();
    $titleColumn = packageTitleColumn();
    $stmt = query("SELECT b.*, p.`$titleColumn` AS package_title, p.price AS package_price, b.return_date, b.phone FROM bookings b LEFT JOIN `$packageTable` p ON b.package_id = p.`$packageIdColumn` WHERE b.user_id = ? ORDER BY b.created_at DESC", [$userId]);
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchAllBookings() {
    $packageTable = packageTable();
    $packageIdColumn = packageIdColumn();
    $titleColumn = packageTitleColumn();
    $stmt = query("SELECT b.*, u.full_name AS customer_name, p.`$titleColumn` AS package_title FROM bookings b LEFT JOIN users u ON b.user_id = u.user_id LEFT JOIN `$packageTable` p ON b.package_id = p.`$packageIdColumn` ORDER BY b.created_at DESC");
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchQueriesByUserId($userId) {
    $stmt = query('SELECT q.*, u.full_name FROM queries q LEFT JOIN users u ON q.user_id = u.user_id WHERE q.user_id = ? ORDER BY q.created_at DESC', [$userId]);
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchAllQueries() {
    $stmt = query('SELECT q.*, u.full_name FROM queries q LEFT JOIN users u ON q.user_id = u.user_id ORDER BY q.created_at DESC');
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchQueryById($queryId) {
    $stmt = query('SELECT q.*, u.full_name FROM queries q LEFT JOIN users u ON q.user_id = u.user_id WHERE q.query_id = ?', [$queryId]);
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function fetchPaymentsByUserId($userId) {
    if (columnExists('payments', 'user_id')) {
        $stmt = query('SELECT * FROM payments WHERE user_id = ? ORDER BY payment_date DESC', [$userId]);
    } else {
        $stmt = query('SELECT p.*, b.package_id, b.booking_id, p.booking_id AS booking_reference, b.total_price, u.full_name FROM payments p LEFT JOIN bookings b ON p.booking_id = b.booking_id LEFT JOIN users u ON b.user_id = u.user_id WHERE b.user_id = ? ORDER BY p.payment_date DESC', [$userId]);
    }
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchAllPayments() {
    $stmt = query('SELECT p.*, u.full_name AS customer_name FROM payments p LEFT JOIN bookings b ON p.booking_id = b.booking_id LEFT JOIN users u ON b.user_id = u.user_id ORDER BY p.payment_date DESC');
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchHotelRecords() {
    $stmt = query('SELECT * FROM hotels ORDER BY hotel_id ASC');
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchTransportRecords() {
    $stmt = query('SELECT * FROM transport ORDER BY transport_id ASC');
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchAllUsers() {
    $stmt = query('SELECT * FROM users ORDER BY user_id ASC');
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function countTotalUsers() {
    $stmt = query('SELECT COUNT(*) AS total FROM users');
    $result = $stmt->get_result();
    return intval($result->fetch_assoc()['total'] ?? 0);
}

function countTotalBookings() {
    $stmt = query('SELECT COUNT(*) AS total FROM bookings');
    $result = $stmt->get_result();
    return intval($result->fetch_assoc()['total'] ?? 0);
}

function sumTotalRevenue() {
    $stmt = query('SELECT COALESCE(SUM(amount), 0) AS total FROM payments');
    $result = $stmt->get_result();
    return floatval($result->fetch_assoc()['total'] ?? 0);
}

function fetchStaffUsers() {
    $stmt = query('SELECT * FROM users WHERE role = ? ORDER BY user_id ASC', ['staff']);
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function insertBooking($userId, $packageId, $travelDate, $returnDate, $guests, $phone, $totalPrice) {
    query(
        'INSERT INTO bookings (user_id, package_id, travel_date, return_date, guests, phone, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
        [$userId, $packageId, $travelDate, $returnDate, $guests, $phone, $totalPrice, 'pending']
    );

    return db()->insert_id;
}

function updateBookingStatus($bookingId, $status) {
    query('UPDATE bookings SET status = ? WHERE booking_id = ?', [$status, $bookingId]);
}

function insertPayment($bookingId, $amount, $paymentMethod, $paymentStatus) {
    if (columnExists('payments', 'user_id')) {
        $booking = fetchBookingById($bookingId);
        query('INSERT INTO payments (booking_id, user_id, amount, payment_method, payment_status) VALUES (?, ?, ?, ?, ?)', [$bookingId, $booking['user_id'] ?? null, $amount, $paymentMethod, $paymentStatus]);
    } else {
        query('INSERT INTO payments (booking_id, amount, payment_method, payment_status) VALUES (?, ?, ?, ?)', [$bookingId, $amount, $paymentMethod, $paymentStatus]);
    }
    return db()->insert_id;
}

function fetchBookingById($bookingId) {
    $packageTable = packageTable();
    $packageIdColumn = packageIdColumn();
    $titleColumn = packageTitleColumn();
    $imageColumn = packageImageColumn();
    $stmt = query("SELECT b.*, u.full_name AS user_name, u.email AS user_email, p.`$titleColumn` AS package_title, p.price AS package_price, p.`$imageColumn` AS package_image FROM bookings b LEFT JOIN users u ON b.user_id = u.user_id LEFT JOIN `$packageTable` p ON b.package_id = p.`$packageIdColumn` WHERE b.booking_id = ?", [$bookingId]);
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function createPackage(array $data) {
    $table = packageTable();
    query(
        "INSERT INTO `$table` (title, location, description, price, image, category, availability, duration, transport, rating, highlights, itinerary, included) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        [$data['title'], $data['location'], $data['description'], $data['price'], $data['image'], $data['category'], $data['availability'], $data['duration'], $data['transport'], $data['rating'], $data['highlights'], $data['itinerary'], $data['included']]
    );
    return db()->insert_id;
}

function updatePackage($packageId, array $data) {
    $table = packageTable();
    query(
        "UPDATE `$table` SET title = ?, location = ?, description = ?, price = ?, image = ?, category = ?, availability = ?, duration = ?, transport = ?, rating = ?, highlights = ?, itinerary = ?, included = ? WHERE package_id = ?",
        [$data['title'], $data['location'], $data['description'], $data['price'], $data['image'], $data['category'], $data['availability'], $data['duration'], $data['transport'], $data['rating'], $data['highlights'], $data['itinerary'], $data['included'], $packageId]
    );
}

function updateUserStatus($userId, $status) {
    query('UPDATE users SET status = ? WHERE user_id = ?', [$status, $userId]);
}

function toggleUserApproval($userId) {
    $user = fetchUserById($userId);
    if (!$user) {
        return;
    }
    $newStatus = $user['status'] ? 0 : 1;
    query('UPDATE users SET status = ? WHERE user_id = ?', [$newStatus, $userId]);
}

function toggleStaffApproval($userId) {
    toggleUserApproval($userId);
}

function fetchUserBookings($email) {
    $packageTable = packageTable();
    $packageIdColumn = packageIdColumn();
    $titleColumn = packageTitleColumn();
    $stmt = query("SELECT b.*, p.`$titleColumn` AS package_title FROM bookings b LEFT JOIN `$packageTable` p ON b.package_id = p.`$packageIdColumn` LEFT JOIN users u ON b.user_id = u.user_id WHERE LOWER(u.email) = LOWER(?) ORDER BY b.created_at DESC", [$email]);
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchUserQueries($email) {
    $stmt = query(
        'SELECT q.* FROM queries q LEFT JOIN users u ON q.user_id = u.user_id WHERE (q.user_id IS NOT NULL AND LOWER(u.email) = LOWER(?)) OR (q.user_id IS NULL AND LOWER(q.email) = LOWER(?)) ORDER BY q.created_at DESC',
        [$email, $email]
    );
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchUserPayments($email) {
    $stmt = query('SELECT p.*, b.package_id FROM payments p LEFT JOIN bookings b ON p.booking_id = b.booking_id LEFT JOIN users u ON b.user_id = u.user_id WHERE LOWER(u.email) = LOWER(?) ORDER BY p.payment_date DESC', [$email]);
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchPackageDisplayName($packageId) {
    $package = fetchPackageById($packageId);
    return $package ? $package['title'] : 'Unknown Package';
}

function createQuery($userId, $email, $subject, $message) {
    query('INSERT INTO queries (user_id, email, subject, message, status) VALUES (?, ?, ?, ?, ?)', [$userId, $email, $subject, $message, 'open']);
    return db()->insert_id;
}

function fetchPackageHighlights($rawHighlights) {
    $items = [];
    if (empty($rawHighlights)) {
        return $items;
    }
    return array_filter(array_map('trim', explode("\n", $rawHighlights)));
}

function fetchPackageItinerary($rawItinerary) {
    $items = [];
    if (empty($rawItinerary)) {
        return $items;
    }
    foreach (explode("\n", $rawItinerary) as $line) {
        if (strpos($line, '|') !== false) {
            [$day, $activity] = explode('|', $line, 2);
            $items[] = ['day' => trim($day), 'activity' => trim($activity)];
        }
    }
    return $items;
}

function fetchPackageIncluded($rawIncluded) {
    return array_filter(array_map('trim', explode("\n", $rawIncluded)));
}
