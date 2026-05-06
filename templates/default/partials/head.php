    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDesc) ?>">
    <meta name="robots"      content="<?= htmlspecialchars($robots ?? 'index, follow') ?>">
    <link rel="canonical"    href="<?= htmlspecialchars($canonicalUrl) ?>">

    <?php if (!empty($prevUrl)): ?>
    <link rel="prev" href="<?= htmlspecialchars($prevUrl) ?>">
    <?php endif; ?>
    <?php if (!empty($nextUrl)): ?>
    <link rel="next" href="<?= htmlspecialchars($nextUrl) ?>">
    <?php endif; ?>

    <?php if (!empty($ogType)): /* Article-specific OG */ ?>
    <meta property="og:type"        content="<?= htmlspecialchars($ogType) ?>">
    <meta property="og:title"       content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDesc) ?>">
    <meta property="og:image"       content="<?= htmlspecialchars($ogImage ?? DEFAULT_OG_IMAGE) ?>">
    <?php if (!empty($ogImageW)): ?>
    <meta property="og:image:width" content="<?= (int)$ogImageW ?>">
    <meta property="og:image:height"content="<?= (int)$ogImageH ?>">
    <?php endif; ?>
    <meta property="og:url"         content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:site_name"   content="<?= htmlspecialchars(SITE_NAME) ?>">
    <meta property="og:locale"      content="<?= SITE_LOCALE ?>">
    <?php if (!empty($ogArticleMeta)): foreach ($ogArticleMeta as $prop => $val): ?>
    <meta property="<?= htmlspecialchars($prop) ?>" content="<?= htmlspecialchars($val) ?>">
    <?php endforeach; endif; ?>
    <?php if (!empty($ogTags)): foreach ($ogTags as $tag): ?>
    <meta property="article:tag" content="<?= htmlspecialchars($tag) ?>">
    <?php endforeach; endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= htmlspecialchars($pageTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDesc) ?>">
    <meta name="twitter:image"       content="<?= htmlspecialchars($ogImage ?? DEFAULT_OG_IMAGE) ?>">
    <?php else: /* Website OG */ ?>
    <meta property="og:type"        content="website">
    <meta property="og:title"       content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDesc) ?>">
    <meta property="og:image"       content="<?= DEFAULT_OG_IMAGE ?>">
    <meta property="og:url"         content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:site_name"   content="<?= htmlspecialchars(SITE_NAME) ?>">
    <meta property="og:locale"      content="<?= SITE_LOCALE ?>">
    <?php endif; ?>

    <?php if (!empty($keywords)): ?>
    <meta name="keywords" content="<?= htmlspecialchars($keywords) ?>">
    <?php endif; ?>
    <?php if (!empty($author)): ?>
    <meta name="author"   content="<?= htmlspecialchars($author) ?>">
    <?php endif; ?>

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json"><?= $schema ?? '{}' ?></script>

    <link rel="stylesheet" href="/article-seo-one/assets/style.css">

