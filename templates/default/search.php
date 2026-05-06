<!DOCTYPE html>
<html lang="<?= SITE_LANG ?>">
<head>
<?php partial('head', compact('pageTitle', 'pageDesc', 'canonicalUrl', 'robots', 'schema', 'pageType', 'ogType', 'alternateUrls')) ?>
</head>
<body>
<div id="reading-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
<?php partial('header') ?>

<div class="container main-wrap">
    <main id="main-content">
        <header class="archive-header">
            <p class="eyebrow"><?= t('search_eyebrow') ?></p>
            <h1><?= htmlspecialchars($query !== '' ? t('search_title', $query) : t('search_empty_title')) ?></h1>
            <p><?= htmlspecialchars($query !== '' ? t('search_desc', $query, $total) : t('search_empty_desc')) ?></p>
            <form class="search-form search-form-wide" action="<?= SITE_URL ?>/search" method="get" role="search">
                <label class="sr-only" for="search-page-input"><?= t('search_label') ?></label>
                <input id="search-page-input" type="search" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="<?= htmlspecialchars(t('search_placeholder')) ?>">
                <button type="submit"><?= t('search_button') ?></button>
            </form>
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
            <p><?= t('search_no_results') ?></p>
            <a class="page-btn active" href="<?= url('home') ?>"><?= t('view_all_articles') ?></a>
        </div>
        <?php endif; ?>
    </main>
    <?php partial('sidebar') ?>
</div>

<?php partial('footer') ?>
</body>
</html>
