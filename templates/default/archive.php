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
        <div class="article-list">
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

        <?php partial('pagination', ['currentPage' => $currentPage, 'totalPages' => $totalPages, 'baseUrl' => $baseArchiveUrl]) ?>
    </main>
    <?php partial('sidebar') ?>
</div>

<?php partial('footer') ?>
</body>
</html>
