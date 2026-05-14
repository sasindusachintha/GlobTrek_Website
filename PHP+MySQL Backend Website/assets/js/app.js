function initSiteFooter() {
    if (document.querySelector('.site-footer')) return;

    const year = new Date().getFullYear();
    const footer = document.createElement('footer');
    footer.className = 'site-footer';
    footer.innerHTML = `
        <div class="container">
            <div class="site-footer-main">
                <div>
                    <a class="site-footer-brand" href="index.php">
                        <img src="assets/images/logo.png" alt="GlobeTrek Logo">
                        <span>GlobeTrek Adventures</span>
                    </a>
                    <p>Curated travel packages, simple booking, and reliable trip support from planning to return.</p>
                </div>
                <nav class="site-footer-links" aria-label="Footer navigation">
                    <a href="index.php">Home</a>
                    <a href="packages.php">Packages</a>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="contact.php#about-us">About Us</a>
                    <a href="contact.php">Contact Us</a>
                    <a href="login.php" data-auth-link>Sign in</a>
                </nav>
            </div>
            <div class="site-footer-bottom">
                <span>GlobeTrek Adventures</span>
                <span>&copy; ${year}. All rights reserved.</span>
            </div>
        </div>
    `;
    document.body.appendChild(footer);
}

window.addEventListener('DOMContentLoaded', initSiteFooter);
