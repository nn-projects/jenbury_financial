<?php
/**
 * Jenbury Financial - Custom Layout
 */
?>
<!DOCTYPE html>
<html>

<head>

    <script>
        const BASE_URL = "<?=$this->Url->build('/', ['fullBase' => false])?>";
    </script>

    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $this->ContentBlock->text('site-name') ?> | <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>
    <?= $this->Html->meta('csrfToken', $this->request->getAttribute('csrfToken')); // Add CSRF token meta tag for AJAX ?>

<!-- Preload key font files -->
    <?= $this->Html->tag('link', null, ['rel' => 'preload', 'href' => $this->Url->webroot('font/raleway-400-latin.woff2'), 'as' => 'font', 'type' => 'font/woff2', 'crossorigin' => 'anonymous']) ?>
    <?= $this->Html->tag('link', null, ['rel' => 'preload', 'href' => $this->Url->webroot('font/raleway-700-latin.woff2'), 'as' => 'font', 'type' => 'font/woff2', 'crossorigin' => 'anonymous']) ?>
    <?= $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css') ?>
    <?= $this->Html->css(['vendors/normalize.min', 'vendors/milligram.min', 'vendors/cake', 'fonts']) ?>
<?= $this->Html->css('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css') ?>
    <?= $this->Html->css('main') ?>
    <?= $this->Html->css('orders') // Added orders.css ?>


    <?php // Conditionally load text override CSS for specific static pages
    $controller = $this->request->getParam('controller');
    $action = $this->request->getParam('action');
    $targetPages = ['about', 'contact', 'faq'];
    if ($controller === 'Pages' && in_array($action, $targetPages)):
        ?>
        <?= $this->Html->css('pages-text-override') ?>
    <?php endif; ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>



</head>

<body>
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="logo" style="display: flex; align-items: center; gap: 1rem;">
                    <a href="<?= $this->Url->build('/') ?>">
                        <?= $this->Html->image('logo.jpg', [
                            'alt' => $this->ContentBlock->text('site-name'),
                            'class' => 'site-logo',
                            'style' => 'height: 48px; width: auto; display: block;'
                        ]) ?>
                    </a>
                    <a href="<?= $this->Url->build('/') ?>" style="text-decoration: none;">
                        <h1 style="margin: 0; font-size: 2.4rem;"><?= $this->ContentBlock->text('site-name') ?></h1>
                    </a>
                </div>

                <nav class="main-nav">
                    <ul style="display: flex; align-items: center; width: 100%; list-style: none; padding: 0; margin: 0;">
                    <ul>
                        <li><?= $this->Html->link($this->ContentBlock->text('navbar-link-courses-text'), ['controller' => 'Courses', 'action' => 'index', 'prefix' => false]) ?> </li>
                        <li><?= $this->Html->link($this->ContentBlock->text('navbar-link-forums-text'), ['controller' => 'ForumCategories', 'action' => 'index', 'prefix' => false]) ?> </li>
                    </ul>
                </nav>
                <div class="user-nav-desktop"> <!-- Renamed from nav-right-group, for desktop -->
                    <a href="<?= $this->Url->build(['controller' => 'Carts', 'action' => 'view', 'prefix' => false]) ?>" aria-label="View Shopping Cart" class="nav-cart-icon nav-cart-icon-desktop"> <!-- Added -desktop class -->
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                    <div class="desktop-nav"> <!-- Contains the user dropdown -->
                        <div class="user-nav">
                            <?php if ($loggedIn): ?>
                                <div class="dropdown">
                                    <button class="dropdown-toggle"
                                    style="font-weight: bold; color: var(--jf-gold-accent);"><?= $currentUser->first_name ?>
                                    <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <?php if ($currentUser->role === 'admin'): ?>
                                            <li><?= $this->Html->link($this->ContentBlock->text('user-dropdown-admin-dashboard-text'), ['controller' => 'Admin', 'action' => 'dashboard']) ?>
                                            </li>
                                            <li class="divider"></li>
                                        <?php endif; ?>
                                        <li><?= $this->Html->link(($currentUser->role === 'admin' ? 'Customer Dashboard' : $this->ContentBlock->text('user-dropdown-dashboard-text')), ['controller' => 'Dashboard', 'action' => 'index', 'prefix' => false]) ?>
                                        </li>
                                        <li><?= $this->Html->link('My Account', ['controller' => 'Users', 'action' => 'account', 'prefix' => false]) ?> <?php // Replaced Profile, Change Password, Purchase History ?>
                                        </li>
                                        <?php if ($currentUser->role === 'admin'): ?>
                                            <li><?= $this->Html->link($this->ContentBlock->text('user-dropdown-manage-site-content-text'), ['controller' => 'Admin', 'action' => 'siteContent']) ?>
                                        </li> <!-- Assuming slug: user-dropdown-manage-site-content-text -->
                                        <?php endif; ?>
                                        <li class="divider"></li>
                                        <li><?= $this->Html->link($this->ContentBlock->text('user-dropdown-logout-text'), ['controller' => 'Users', 'action' => 'logout','prefix' => false]) ?>
                                    </li>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <?= $this->Html->link($this->ContentBlock->text('user-nav-login-text'), ['controller' => 'Users', 'action' => 'login'], ['class' => 'login-button']) ?>
                                <?= $this->Html->link($this->ContentBlock->text('user-nav-register-text'), ['controller' => 'Users', 'action' => 'register'], ['class' => 'signup-button']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div> <!-- End user-nav-desktop -->

                <!-- Mobile Header Actions -->
                <div class="mobile-header-actions">
                    <a href="<?= $this->Url->build(['controller' => 'Carts', 'action' => 'view', 'prefix' => false]) ?>" aria-label="View Shopping Cart" class="nav-cart-icon nav-cart-icon-mobile">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                    <button class="navbar-toggler" type="button" id="hamburger-menu" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <button type="button" class="close-menu-btn" id="close-navbarNav" aria-label="Close menu">&times;</button>
                    <ul>
                        <li><?= $this->Html->link($this->ContentBlock->text('navbar-link-courses-text'), ['controller' => 'Courses', 'action' => 'index']) ?></li>
                        <li><?= $this->Html->link($this->ContentBlock->text('navbar-link-forums-text'), ['controller' => 'ForumCategories', 'action' => 'index']) ?></li>
                        <?php if ($loggedIn): ?>
                            <?php if ($currentUser->role === 'admin'): ?>
                                <li><?= $this->Html->link($this->ContentBlock->text('user-dropdown-admin-dashboard-text'), ['controller' => 'Admin', 'action' => 'dashboard']) ?></li>
                                <li><?= $this->Html->link($this->ContentBlock->text('user-dropdown-manage-site-content-text'), ['controller' => 'Admin', 'action' => 'siteContent']) ?></li> <!-- Assuming slug: user-dropdown-manage-site-content-text -->
                                <li class="divider"></li>
                            <?php endif; ?>
                            <li><?= $this->Html->link(($currentUser->role === 'admin' ? 'Customer Dashboard' : $this->ContentBlock->text('user-dropdown-dashboard-text')), ['controller' => 'Dashboard', 'action' => 'index', 'prefix' => false]) ?></li>
                            <li><?= $this->Html->link('My Account', ['controller' => 'Users', 'action' => 'account', 'prefix' => false]) ?></li> <?php // Replaced Profile, Change Password, Purchase History ?>
                            <?php if ($currentUser->role === 'admin'): ?>
                            <?php endif; ?>
                            <li class="divider"></li>
                            <li><?= $this->Html->link($this->ContentBlock->text('user-dropdown-logout-text'), ['controller' => 'Users', 'action' => 'logout']) ?></li>
                            <?php else: ?>
                            <?= $this->Html->link($this->ContentBlock->text('user-nav-login-text'), ['controller' => 'Users', 'action' => 'login'], ['class' => 'login-button']) ?>
                            <?= $this->Html->link($this->ContentBlock->text('user-nav-register-text'), ['controller' => 'Users', 'action' => 'register'], ['class' => 'signup-button']) ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container-fluid">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-columns">
                    <div class="footer-column footer-info">
                        <h3>JENBURY FINANCIAL</h3>
                        <p>
                            <?= $this->ContentBlock->text('footer-copyright-prefix') ?><?= date('Y') ?><?= $this->ContentBlock->text('footer-copyright-suffix') ?><?= $this->ContentBlock->text('footer-abn') ?><?= $this->ContentBlock->text('footer-corp-rep-prefix') ?>
                            <?= $this->ContentBlock->text('footer-corp-rep-no') ?><?= $this->ContentBlock->text('footer-corp-rep-suffix') ?><?= $this->ContentBlock->text('footer-afsl') ?><?= $this->ContentBlock->text('footer-afsl-suffix') ?>
                        </p>
                        <p>
                            <?= $this->ContentBlock->text('footer-phone-prefix') ?><a href="tel:<?= $this->ContentBlock->text('footer-phone-number') ?>"><?= $this->ContentBlock->text('footer-phone-number') ?></a> | <?= $this->ContentBlock->text('footer-email-prefix') ?><a href="mailto:<?= $this->ContentBlock->text('footer-email-address') ?>"><?= $this->ContentBlock->text('footer-email-address') ?></a>
                        </p>
                        <div class="footer-links">
                            <a href="<?= $this->ContentBlock->text('footer-link-fsg-url') ?>"
                                target="_blank" rel="noopener noreferrer">Financial Services Guide</a> |
                            <a href="<?= $this->ContentBlock->text('footer-link-disclaimer-url') ?>" target="_blank"
                                rel="noopener noreferrer">Disclaimer</a> |
                            <a href="<?= $this->ContentBlock->text('footer-link-privacy-url') ?>" target="_blank"
                                rel="noopener noreferrer">Privacy Policy</a> |
                            <a href="<?= $this->ContentBlock->text('footer-link-about-url') ?>" target="_blank" rel="noopener noreferrer">About</a> |
                            <a href="<?= $this->ContentBlock->text('footer-link-faq-url') ?>" target="_blank" rel="noopener noreferrer">FAQ</a> |
                            <a href="<?= $this->ContentBlock->text('footer-link-contact-url') ?>" target="_blank" rel="noopener noreferrer">Contact</a>
                        </div>
                    </div>
                    <div class="footer-column footer-map">
                        <iframe
                          src="https://maps.google.com/maps?q=2/173%20Boronia%20Rd,%20Boronia%20VIC%203155&t=&z=15&ie=UTF8&iwloc=&output=embed"
                          width="100%"
                          height="250"
                          style="border:0;"
                          allowfullscreen=""
                          loading="lazy"
                          referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <?= $this->Html->script(['jenbury']) ?>
    <?= $this->Html->script(['form-validation']) // Add form validation script ?>
    <?= $this->Html->script(['password-toggle']) // Add password toggle script ?>
    <?= $this->Html->script(['course-progress']) // Re-enabled course progress script ?>

    <?php // Conditionally load manage users script ?>
    <?php if ($this->request->getParam('controller') === 'Admin' && $this->request->getParam('action') === 'manageUsers'): ?>
        <?= $this->Html->script(['admin_manage_users']) ?>
    <?php endif; ?>

    <?php // Conditionally load session timeout warning script for logged-in users ?>
    <?php if ($loggedIn): ?>
        <?= $this->Html->script('session-timeout-warning.js') ?>
    <?php endif; ?>

    <?php // Conditionally load manage modules script ?>
    <?php if ($this->request->getParam('controller') === 'Admin' && $this->request->getParam('action') === 'manageModules'): ?>
        <?= $this->Html->script('https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js') ?>
        <?= $this->Html->script(['admin_manage_modules']) ?>
    <?php endif; ?>

    <?= $this->fetch('scriptBottom') ?>
</body>

</html>


<style>
.navbar-toggler {
    display: none;
    font-size: 2rem;
    background: none;
    border: none;
    color: var(--jf-gold-accent);
    margin-left: auto;
}

.navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(212, 175, 55, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    display: inline-block;
    width: 30px;
    height: 30px;
}

.collapse.navbar-collapse {
    display: none;
    flex-direction: column;
    background: white;
    padding: 1rem;
    border: 1px solid #ddd;
    width: 100%;
    margin-top: 1rem;
}

.collapse.navbar-collapse.show {
    display: flex;
}

.collapse.navbar-collapse ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.collapse.navbar-collapse li {
    padding: 0.5rem 0;
}

.collapse.navbar-collapse a {
    display: block;
    color: #333;
    text-decoration: none;
}

/* Responsive breakpoint */
@media (max-width: 768px) {
    .desktop-nav,
    .main-nav {
        display: none;
    }

    .navbar-toggler {
        display: block;
    }
    /* Removed extra closing brace here */
} /* End first @media */

/* Footer Columns - Default (Desktop) */
.footer-columns {
    display: flex;
    gap: 2rem; /* Adjust gap as needed */
    /* Removed diagnostic border */
}

.footer-column {
    flex: 1; /* Each column tries to take equal space */
    min-width: 280px; /* Prevent columns from becoming too narrow */
}

.footer-map iframe {
    width: 100%; /* Make iframe responsive within its column */
    height: 250px; /* Maintain specified height */
    border: 0;
}

/* Responsive Footer: Stack columns below 768px */
@media (max-width: 768px) {
    .footer-columns {
        flex-direction: column; /* Stack columns vertically */
        gap: 1rem; /* Adjust gap for stacked layout */
    }
    /* No specific changes needed for .footer-column here */
}

/* Custom styles for user navigation text size */
.user-nav-desktop .dropdown-toggle {
    font-size: 1.6rem; /* Increased from default */
}

.user-nav-desktop .dropdown-menu a {
    font-size: 1.5rem; /* Increased from default */
    padding: 0.5rem 1rem; /* Adjusted padding for larger font */
}

/* Mobile navigation menu links */
/* This targets all links (Courses, Forums, User links, Login/Register) in the slide-out #navbarNav */
#navbarNav ul li a,
#navbarNav .login-button,
#navbarNav .signup-button {
    font-size: 1.6rem; /* Increased from default */
}

</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('hamburger-menu');
    const closeBtn = document.getElementById('close-navbarNav');
    const navbar = document.getElementById('navbarNav');
    const body = document.body;

    function openMenu() {
        navbar.classList.add('show');
        body.classList.add('mobile-menu-open');
    }

    function closeMenu() {
        navbar.classList.remove('show');
        body.classList.remove('mobile-menu-open');
    }

    if (toggleBtn && navbar) {
        toggleBtn.addEventListener('click', () => {
            if (navbar.classList.contains('show')) {
                closeMenu();
            } else {
                openMenu();
            }
        });
    }

    if (closeBtn && navbar) {
        closeBtn.addEventListener('click', () => {
            closeMenu();
        });
    }

    if (navbar) {
        document.querySelectorAll('#navbarNav a').forEach(link => {
            link.addEventListener('click', () => {
                closeMenu();
                // Allow default link behavior to proceed
            });
        });
    }
});

</script>