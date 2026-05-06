<!DOCTYPE html>
<html lang="<?= SITE_LANG ?>">
<head>
<?php
$pageTitle = $pageTitle ?? t('404_title') . ' | ' . SITE_NAME;
$pageDesc = $message ?? t('404_not_found');
$canonicalUrl = url('home');
$robots = 'noindex, nofollow';
$schema = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $pageTitle,
    'description' => $pageDesc,
    'isPartOf' => ['@type' => 'WebSite', 'name' => SITE_NAME, 'url' => SITE_URL],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
partial('head', compact('pageTitle', 'pageDesc', 'canonicalUrl', 'robots', 'schema'));
?>
</head>
<body>
<?php partial('header') ?>

<div class="container">
    <section class="empty-state not-found">
        <p class="eyebrow"><?= t('404_title') ?></p>
        <h1>404</h1>
        <p><?= htmlspecialchars($message ?? t('404_not_found')) ?></p>
        <form class="search-form search-form-wide" action="<?= SITE_URL ?>/search" method="get" role="search">
            <label class="sr-only" for="not-found-search"><?= t('search_label') ?></label>
            <input id="not-found-search" type="search" name="q" placeholder="<?= htmlspecialchars(t('search_placeholder')) ?>">
            <button type="submit"><?= t('search_button') ?></button>
        </form>
        <a href="<?= url('home') ?>" class="page-btn active"><?= t('404_back') ?></a>
    </section>
</div>

<?php partial('footer') ?>
</body>
</html>
