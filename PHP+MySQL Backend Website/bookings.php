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

$_GET['section'] = 'bookings';
require __DIR__ . '/dashboard.php';
