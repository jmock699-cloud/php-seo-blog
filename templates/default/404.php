<!DOCTYPE html>
<html lang="<?= SITE_LANG ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? t('404_title')) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/article-seo-one/assets/style.css">
</head>
<body>

<?php partial('header') ?>

<div class="container" style="padding:4rem 1.25rem; text-align:center;">
    <h1 style="font-size:4rem; color:#e2e8f0; margin-bottom:1rem;">404</h1>
    <p style="font-size:1.2rem; color:#64748b; margin-bottom:2rem;">
        <?= htmlspecialchars($message ?? t('404_not_found')) ?>
    </p>
    <a href="<?= url('home') ?>" class="page-btn active"><?= t('404_back') ?></a>
</div>

<?php partial('footer') ?>

</body>
</html>

