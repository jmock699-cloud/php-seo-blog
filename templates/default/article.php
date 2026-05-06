<!DOCTYPE html>
<html lang="<?= SITE_LANG ?>">
<head>
<?php partial('head', [
    'pageTitle'      => $pageTitle,
    'pageDesc'       => $pageDesc,
    'canonicalUrl'   => $canonicalUrl,
    'schema'         => $schema,
    'robots'         => 'index, follow',
    'ogType'         => 'article',
    'ogImage'        => $coverImage,
    'ogImageW'       => 1200,
    'ogImageH'       => 630,
    'keywords'       => $article['keywords'],
    'author'         => $article['author']['name'],
    'ogArticleMeta'  => [
        'article:published_time' => $publishedIso,
        'article:modified_time'  => $updatedIso,
        'article:author'         => $article['author']['name'],
    ],
    'ogTags'         => $article['tags'],
]) ?>
</head>
<body>

<!-- Reading progress bar -->
<div id="reading-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>

<?php partial('header') ?>

<div class="container main-wrap">
    <main id="main-content">

        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="<?= t('aria_breadcrumb') ?>">
            <ol>
                <li><a href="<?= url('home') ?>"><?= t('home') ?></a></li>
                <li>
                    <a href="<?= htmlspecialchars($categoryUrl) ?>">
                        <?= htmlspecialchars($article['category']['name']) ?>
                    </a>
                </li>
                <li aria-current="page"><?= htmlspecialchars($article['title']) ?></li>
            </ol>
        </nav>

        <!-- Article -->
        <article class="article" itemscope itemtype="https://schema.org/Article">

            <header class="article-header">
                <h1 class="article-title" itemprop="headline">
                    <?= htmlspecialchars($article['title']) ?>
                </h1>

                <div class="article-meta">
                    <span class="author" itemprop="author"
                          itemscope itemtype="https://schema.org/Person">
                        <?= t('author_label') ?>
                        <a href="<?= htmlspecialchars($authorUrl) ?>" itemprop="url">
                            <span itemprop="name"><?= htmlspecialchars($article['author']['name']) ?></span>
                        </a>
                    </span>
                    <time datetime="<?= $publishedIso ?>" itemprop="datePublished">
                        <?= t('published_label') ?> <?= $publishedFmt ?>
                    </time>
                    <?php if ($updatedFmt !== $publishedFmt): ?>
                    <time datetime="<?= $updatedIso ?>" itemprop="dateModified">
                        <?= t('updated_label') ?> <?= $updatedFmt ?>
                    </time>
                    <?php endif; ?>
                    <span class="reading-time"><?= t('reading_time', $readTime) ?></span>
                    <span class="reading-progress-text" aria-live="polite" title="Reading progress">0%</span>
                </div>

                <!-- Share button (shown by JS) -->
                <button id="share-btn" class="share-btn" aria-label="Share this article">
                    🔗 <?= t('share') ?>
                </button>

                <?php if ($coverImage): ?>
                <img class="article-cover"
                     src="<?= htmlspecialchars($coverImage) ?>"
                     alt="<?= htmlspecialchars($article['title']) ?>"
                     width="1200" height="630"
                     itemprop="image"
                     loading="lazy">
                <?php endif; ?>
            </header>

            <!-- Table of Contents -->
            <?php if (count($toc) > 2): ?>
            <nav class="toc" aria-label="<?= t('aria_toc') ?>" id="toc">
                <p class="toc-title"><?= t('toc_title') ?></p>
                <ol>
                    <?php foreach ($toc as $h):
                        $level  = $h[1] ?? 2;
                        $hId    = $h[2] ?? ($h['auto_id'] ?? '');
                        $hTitle = strip_tags($h[3] ?? $h[2] ?? '');
                    ?>
                    <li <?= $level == 3 ? 'class="toc-sub"' : '' ?>>
                        <a href="#<?= htmlspecialchars($hId) ?>"><?= htmlspecialchars($hTitle) ?></a>
                    </li>
                    <?php endforeach; ?>
                </ol>
            </nav>
            <?php endif; ?>

            <!-- Article body -->
            <div class="article-content" itemprop="articleBody">
                <?= $bodyContent ?>
            </div>

            <!-- Tags -->
            <?php if (!empty($article['tags'])): ?>
            <div class="article-tags">
                <span><?= t('tags_label') ?></span>
                <?php foreach ($article['tags'] as $tag): ?>
                <a href="<?= htmlspecialchars(url('tag', ['slug' => slugify($tag)])) ?>"
                   class="tag" rel="tag">
                    <?= htmlspecialchars($tag) ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Author card -->
            <div class="author-card" itemscope itemtype="https://schema.org/Person">
                <div class="author-avatar">
                    <img src="<?= SITE_URL ?>/assets/avatars/<?= htmlspecialchars($article['author']['slug']) ?>.jpg"
                         alt="<?= htmlspecialchars($article['author']['name']) ?>"
                         width="60" height="60"
                         loading="lazy"
                         onerror="this.src='<?= SITE_URL ?>/assets/avatars/default.jpg'">
                </div>
                <div class="author-info">
                    <strong itemprop="name"><?= htmlspecialchars($article['author']['name']) ?></strong>
                    <p><?= t('author_bio') ?></p>
                    <a href="<?= htmlspecialchars($authorUrl) ?>" itemprop="url">
                        <?= t('view_all_articles') ?>
                    </a>
                </div>
            </div>

        </article>

        <!-- Related articles -->
        <?php if (!empty($related)): ?>
        <section class="related-articles">
            <h2><?= t('related_articles') ?></h2>
            <div class="related-grid">
                <?php foreach ($related as $r): ?>
                <article class="related-card">
                    <a href="<?= htmlspecialchars(url('article', ['slug' => $r['slug']])) ?>">
                        <img src="<?= htmlspecialchars($r['cover'] ?: DEFAULT_OG_IMAGE) ?>"
                             alt="<?= htmlspecialchars($r['title']) ?>"
                             width="400" height="210"
                             loading="lazy">
                        <h3><?= htmlspecialchars($r['title']) ?></h3>
                        <p><?= htmlspecialchars(excerpt($r['description'], 80)) ?></p>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

    </main>

    <?php partial('sidebar') ?>
</div>

<?php partial('footer') ?>

</body>
</html>

