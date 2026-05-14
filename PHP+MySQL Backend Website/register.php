<?php
define('IN_SITE', true);
require_once __DIR__ . '/includes/functions.php';
$errorMessage = '';

$successMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['registerName'] ?? '');
    $email = trim($_POST['registerEmail'] ?? '');
    $password = trim($_POST['registerPassword'] ?? '');
    $confirm = trim($_POST['registerConfirmPassword'] ?? '');
    $role = trim($_POST['registerRole'] ?? '');

    if (!$name || !$email || !$password || !$confirm || !$role) {
        $errorMessage = 'Please complete all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $errorMessage = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $errorMessage = 'Passwords do not match.';
    } elseif (fetchUserByEmail($email)) {
        $errorMessage = 'An account with this email already exists.';
    } else {
        createUser($name, $email, $password, $role);
        $successMessage = $role === 'user' ? 'Registration complete. Please log in.' : 'Registration submitted. Admin approval is required.';
    }
}
$activePage = 'register';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | GlobeTrek</title>
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
                                    <span
                                        class="badge bg-white text-primary px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2">
                                        <img src="assets/images/logo.png" width="20" height="20" alt="Logo">
                                        GlobeTrek Adventures
                                    </span>
                                </div>
                                <h1 class="display-6 fw-bold">Create an account</h1>
                                <p class="mt-4">Join GlobeTrek and start booking unforgettable trips with exclusive
                                    offers, curated accommodations and seamless support.</p>
                            </div>
                        </div>
                        <div class="col-lg-6 page-form">
                            <h2>Register</h2>
                            <form id="registerForm" method="post">
                                <div class="mb-3">
                                    <input id="registerName" name="registerName" type="text"
                                        class="form-control form-control-lg" placeholder="Name"
                                        value="<?= h($_POST['registerName'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <input id="registerEmail" name="registerEmail" type="email"
                                        class="form-control form-control-lg" placeholder="Email"
                                        value="<?= h($_POST['registerEmail'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <input id="registerPassword" name="registerPassword" type="password"
                                        class="form-control form-control-lg" placeholder="Password">
                                </div>
                                <div class="mb-3">
                                    <input id="registerConfirmPassword" name="registerConfirmPassword" type="password"
                                        class="form-control form-control-lg" placeholder="Confirm Password">
                                </div>
                                <div class="mb-4">
                                    <select id="registerRole" name="registerRole" class="form-select form-select-lg">
                                        <option value="">Choose role</option>
                                        <option value="user" <?= isset($_POST['registerRole']) && $_POST['registerRole'] === 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="staff" <?= isset($_POST['registerRole']) && $_POST['registerRole'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                                        <option value="admin" <?= isset($_POST['registerRole']) && $_POST['registerRole'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </div>
                                <?php if ($errorMessage): ?>
                                    <div id="registerMessage" class="mb-3 text-danger"><?= h($errorMessage) ?></div>
                                <?php elseif ($successMessage): ?>
                                    <div id="registerMessage" class="mb-3 text-success"><?= h($successMessage) ?></div>
                                <?php else: ?>
                                    <div id="registerMessage" class="mb-3 text-danger"></div>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-primary btn-lg w-100">Register</button>
                                <p class="text-center text-muted mt-4">Already have an account? <a
                                        href="login.php">Login</a></p>
                            </form>
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