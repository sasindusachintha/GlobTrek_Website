<?php
if (!defined('IN_SITE')) {
    exit('Restricted access');
}
$user = currentUser();
$active = $activePage ?? '';
?>
<nav class="navbar navbar-expand-lg bg-white sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="assets/images/logo.png" alt="GlobeTrek Logo" class="me-2 logo">
            GlobeTrek Adventures
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link <?= $active === 'home' ? 'active' : '' ?>" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link <?= $active === 'packages' ? 'active' : '' ?>" href="packages.php">Packages</a></li>
                <li class="nav-item"><a class="nav-link <?= $active === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?= $active === 'contact' ? 'active' : '' ?>" href="contact.php">Contact</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php#about-us">About Us</a></li>
                <li class="nav-item" id="authItem">
                    <?php if ($user): ?>
                        <a class="nav-link" href="logout.php">Logout</a>
                    <?php else: ?>
                        <a class="nav-link <?= $active === 'login' ? 'active' : '' ?>" href="login.php">Sign in</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>
