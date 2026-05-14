<?php
define('IN_SITE', true);
require_once __DIR__ . '/includes/functions.php';

$errorMessage = '';
$redirect = 'dashboard.php'; // default safe page

function safeLoginRedirect($value) {
    $value = trim((string)$value);

    if ($value === '' || strpos($value, '.html') !== false || preg_match('/^[a-z][a-z0-9+.-]*:/i', $value)) {
        return 'dashboard.php';
    }

    if (str_starts_with($value, '/') || preg_match('/^[A-Za-z0-9_\/.-]+\.php(\?.*)?(#.*)?$/', $value)) {
        return $value;
    }

    return 'dashboard.php';
}

// =========================
// GET REDIRECT (SAFE)
// =========================
if (isset($_GET['redirect'])) {
    $redirect = $_GET['redirect'];
}

$redirect = safeLoginRedirect($redirect);

// =========================
// POST HANDLING
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['loginEmail'] ?? '');
    $password = trim($_POST['loginPassword'] ?? '');

    // POST redirect override (SAFE)
    if (isset($_POST['redirect'])) {
        $redirect = $_POST['redirect'];
    }

    $redirect = safeLoginRedirect($redirect);

    // =========================
    // VALIDATION
    // =========================
    if (!$email || !$password) {
        $errorMessage = 'Please enter both email and password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } else {

        $user = loginUser($email, $password);

        if (!$user) {
            $errorMessage = 'Invalid email or password.';
        } elseif ($user['role'] !== 'customer' && intval($user['status']) === 0) {
            $errorMessage = 'Your account is awaiting admin approval.';
        } else {

            setAuthUser($user);

            // FINAL SAFE REDIRECT
            $target = safeLoginRedirect($redirect);

            redirect($target);
        }
    }
}

$activePage = 'login';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GlobeTrek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main class="auth-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="row g-0 auth-card">
                        <div class="col-lg-6 auth-side p-5 d-flex flex-column justify-content-center">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <span class="badge bg-white text-primary px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2">
                                        <img src="assets/images/logo.png" width="20" height="20" alt="Logo">
                                        GlobeTrek Adventures
                                    </span>
                                </div>
                                <h1 class="display-6 fw-bold">Welcome back</h1>
                                <p class="mt-4">Sign in to your GlobeTrek account to view your bookings, manage your trips, and access exclusive offers.</p>
                            </div>
                        </div>
                        <div class="col-lg-6 page-form">
                            <h2>Sign In</h2>
                            <form id="loginForm" method="post">
                                <input type="hidden" id="loginRedirect" name="redirect" value="<?= h($redirect) ?>">
                                <div class="mb-3">
                                    <input id="loginEmail" name="loginEmail" type="email" class="form-control form-control-lg" placeholder="Email" value="<?= h($_POST['loginEmail'] ?? '') ?>" required>
                                </div>
                                <div class="mb-4">
                                    <input id="loginPassword" name="loginPassword" type="password" class="form-control form-control-lg" placeholder="Password" required>
                                </div>
                                <?php if ($errorMessage): ?>
                                    <div id="loginMessage" class="mb-3 alert alert-danger" role="alert"><?= h($errorMessage) ?></div>
                                <?php else: ?>
                                    <div id="loginMessage" class="mb-3 alert alert-danger d-none" role="alert"></div>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-primary btn-lg w-100">Sign In</button>
                                <p class="text-center text-muted mt-4">Don't have an account? <a href="register.php">Register here</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="assets/js/app.js"></script>
</body>

</html>
