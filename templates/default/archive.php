<!DOCTYPE html>
<html lang="<?= SITE_LANG ?>">
<head>
<?php partial('head', compact('pageTitle', 'pageDesc', 'canonicalUrl', 'robots', 'prevUrl', 'nextUrl', 'schema', 'pageType', 'ogType', 'alternateUrls')) ?>
</head>
<body>
<div id="reading-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
<?php partial('header', ['activeNav' => $archiveType === 'category' ? 'category' : '']) ?>

<div class="container main-wrap">
    <main id="main-content">
        <nav class="breadcrumb" aria-label="<?= t('aria_breadcrumb') ?>">
            <ol>
                <li><a href="<?= url('home') ?>"><?= t('home') ?></a></li>
                <li aria-current="page"><?= htmlspecialchars($archiveTitle) ?></li>
            </ol>
        </nav>

        <header class="archive-header">
            <p class="eyebrow"><?= htmlspecialchars(t('archive_' . $archiveType)) ?></p>
            <h1><?= htmlspecialchars($archiveTitle) ?></h1>
            <p><?= htmlspecialchars($archiveDesc) ?></p>
        </header>

        <?php if (!empty($articles)): ?>
        <div class="article-list" data-load-more-list>
            <?php foreach ($articles as $a): ?>
                <?php partial('article-card', compact('a')) ?>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <h2><?= t('no_articles_title') ?></h2>
            <p><?= t('no_articles_desc') ?></p>
            <a class="page-btn active" href="<?= url('home') ?>"><?= t('view_all_articles') ?></a>
        </div>
        <?php endif; ?>

        <?php if ($currentPage < $totalPages): ?>
        <div class="load-more-wrap" data-load-more data-current-page="<?= (int)$currentPage ?>" data-total-pages="<?= (int)$totalPages ?>">
            <a class="load-more-btn"
               href="<?= htmlspecialchars($baseArchiveUrl . (str_contains($baseArchiveUrl, '?') ? '&' : '?') . 'page=' . ($currentPage + 1)) ?>"
               data-load-more-next
               data-loading-label="<?= htmlspecialchars(t('loading_more_articles')) ?>"
               data-complete-label="<?= htmlspecialchars(t('all_articles_loaded')) ?>"
               data-error-label="<?= htmlspecialchars(t('load_more_error')) ?>">
                <?= t('load_more_articles') ?>
            </a>
            <span class="load-more-status" data-load-more-status role="status" aria-live="polite"></span>
        </div>
        <?php endif; ?>

        <?php partial('pagination', ['currentPage' => $currentPage, 'totalPages' => $totalPages, 'baseUrl' => $baseArchiveUrl]) ?>
    </main>
    <?php partial('sidebar') ?>
</div>

<?php partial('footer') ?>
</body>
</html>
