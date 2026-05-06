<header class="site-header">
    <div class="container">
        <a class="logo" href="<?= url('home') ?>"><?= htmlspecialchars(SITE_NAME) ?></a>

        <!-- Mobile hamburger -->
        <button id="nav-toggle" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>

        <nav id="main-nav" aria-label="<?= t('aria_nav_main') ?>">
            <ul>
                <li><a href="<?= url('home') ?>"
                       <?= ($activeNav ?? '') === 'home' ? 'aria-current="page"' : '' ?>>
                    <?= t('nav_home') ?>
                </a></li>
                <li><a href="<?= url('home') ?>#topics"
                       <?= ($activeNav ?? '') === 'category' ? 'aria-current="page"' : '' ?>>
                    <?= t('nav_category') ?>
                </a></li>
                <li><a href="<?= url('home') ?>#main-content"
                       <?= ($activeNav ?? '') === 'about' ? 'aria-current="page"' : '' ?>>
                    <?= t('nav_about') ?>
                </a></li>
            </ul>
        </nav>

        <div style="display:flex;align-items:center;gap:.5rem;flex-shrink:0">
            <!-- Dark / light mode toggle -->
            <button class="theme-toggle" aria-label="Switch to dark mode" aria-pressed="false">🌙</button>

            <!-- Language dropdown -->
            <div class="lang-switcher">
                <button class="lang-current" aria-haspopup="listbox" aria-expanded="false"
                        aria-label="<?= t('lang_switch_label') ?>">
                    <span class="lang-flag"><?= strtoupper($GLOBALS['_lang']) ?></span>
                    <span class="lang-label"><?= htmlspecialchars(t('lang_' . $GLOBALS['_lang'])) ?></span>
                    <span class="lang-arrow" aria-hidden="true">▾</span>
                </button>
                <ul class="lang-dropdown" role="listbox" aria-label="<?= t('lang_switch_label') ?>">
                    <?php foreach (AVAILABLE_LANGS as $l): ?>
                    <li role="option" aria-selected="<?= $GLOBALS['_lang'] === $l ? 'true' : 'false' ?>">
                        <a href="<?= htmlspecialchars(lang_url($l)) ?>"
                           hreflang="<?= $l ?>"
                           class="<?= $GLOBALS['_lang'] === $l ? 'active' : '' ?>">
                            <?= htmlspecialchars(t('lang_' . $l)) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</header>
