    <?php
    $pageType = $pageType ?? 'website';
    $ogType = $ogType ?? ($pageType === 'article' ? 'article' : 'website');
    $metaImage = $ogImage ?? DEFAULT_OG_IMAGE;
    $alternateUrls = $alternateUrls ?? [];
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#2563eb">

    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDesc) ?>">
    <meta name="robots"      content="<?= htmlspecialchars($robots ?? 'index, follow') ?>">
    <link rel="canonical"    href="<?= htmlspecialchars($canonicalUrl) ?>">

    <?php foreach (AVAILABLE_LANGS as $langCode): ?>
    <link rel="alternate" hreflang="<?= htmlspecialchars($langCode) ?>" href="<?= htmlspecialchars($alternateUrls[$langCode] ?? lang_url($langCode)) ?>">
    <?php endforeach; ?>
    <link rel="alternate" hreflang="x-default" href="<?= htmlspecialchars($alternateUrls[DEFAULT_LANG] ?? $canonicalUrl) ?>">

    <?php if (!empty($prevUrl)): ?>
    <link rel="prev" href="<?= htmlspecialchars($prevUrl) ?>">
    <?php endif; ?>
    <?php if (!empty($nextUrl)): ?>
    <link rel="next" href="<?= htmlspecialchars($nextUrl) ?>">
    <?php endif; ?>

    <meta property="og:type"        content="<?= htmlspecialchars($ogType) ?>">
    <meta property="og:title"       content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDesc) ?>">
    <meta property="og:image"       content="<?= htmlspecialchars($metaImage) ?>">
    <?php if (!empty($ogImageW)): ?>
    <meta property="og:image:width" content="<?= (int)$ogImageW ?>">
    <meta property="og:image:height"content="<?= (int)($ogImageH ?? $ogImageW) ?>">
    <?php endif; ?>
    <meta property="og:url"         content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:site_name"   content="<?= htmlspecialchars(SITE_NAME) ?>">
    <meta property="og:locale"      content="<?= SITE_LOCALE ?>">
    <?php if ($pageType === 'article' && !empty($ogArticleMeta)): foreach ($ogArticleMeta as $prop => $val): ?>
    <meta property="<?= htmlspecialchars($prop) ?>" content="<?= htmlspecialchars($val) ?>">
    <?php endforeach; endif; ?>
    <?php if ($pageType === 'article' && !empty($ogTags)): foreach ($ogTags as $tag): ?>
    <meta property="article:tag" content="<?= htmlspecialchars($tag) ?>">
    <?php endforeach; endif; ?>

    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= htmlspecialchars($pageTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDesc) ?>">
    <meta name="twitter:image"       content="<?= htmlspecialchars($metaImage) ?>">

    <?php if ($pageType === 'article' && !empty($keywords)): ?>
    <meta name="keywords" content="<?= htmlspecialchars($keywords) ?>">
    <?php endif; ?>
    <?php if ($pageType === 'article' && !empty($author)): ?>
    <meta name="author"   content="<?= htmlspecialchars($author) ?>">
    <?php endif; ?>

    <?php if (!empty($schema)): ?>
    <script type="application/ld+json"><?= $schema ?></script>
    <?php endif; ?>

    <link rel="preconnect" href="https://schema.org">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/style.css">
