<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(SITE_NAME) ?>. All Rights Reserved.</p>
        <nav aria-label="<?= t('aria_nav_footer') ?>">
            <a href="<?= SITE_URL ?>/sitemap.xml"><?= t('footer_sitemap') ?></a>
            <a href="<?= url('author', ['slug' => 'privacy']) ?>"><?= t('footer_privacy') ?></a>
        </nav>
    </div>
</footer>

<!-- Back to top -->
<button id="back-to-top" aria-label="Back to top">↑</button>

<script src="<?= SITE_URL ?>/assets/app.js" defer></script>
