<!DOCTYPE html>
<html lang="<?= SITE_LANG ?>">
<head>
<?php partial('head', compact('pageTitle', 'pageDesc', 'canonicalUrl', 'robots', 'prevUrl', 'nextUrl', 'schema')) ?>
</head>
<body>

<!-- Reading progress bar -->
<div id="reading-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>

<?php partial('header', ['activeNav' => 'home']) ?>

<div class="container main-wrap">
    <main id="main-content">

        <h1 class="list-title">
            <?= $currentPage > 1 ? t('latest_articles_paged', $currentPage) : t('latest_articles') ?>
        </h1>

        <!-- Article list -->
        <div class="article-list">
            <?php foreach ($articles as $a): ?>
            <article class="article-card" itemscope itemtype="https://schema.org/Article">

                <?php if (!empty($a['cover'])): ?>
                <a href="<?= htmlspecialchars(url('article', ['slug' => $a['slug']])) ?>" class="card-cover-link">
                    <img class="card-cover"
                         src="<?= htmlspecialchars($a['cover']) ?>"
                         alt="<?= htmlspecialchars($a['title']) ?>"
                         width="800" height="420"
                         loading="lazy"
                         itemprop="image">
                </a>
                <?php endif; ?>

                <div class="card-body">
                    <div class="card-meta">
                        <a class="card-category"
                           href="<?= htmlspecialchars(url('category', ['slug' => $a['category']['slug']])) ?>">
                            <?= htmlspecialchars($a['category']['name']) ?>
                        </a>
                        <time datetime="<?= $a['published_at'] ?>" itemprop="datePublished">
                            <?= date('Y-m-d', strtotime($a['published_at'])) ?>
                        </time>
                        <span><?= t('min_read', reading_time($a['content'])) ?></span>
                    </div>

                    <h2 class="card-title" itemprop="headline">
                        <a href="<?= htmlspecialchars(url('article', ['slug' => $a['slug']])) ?>">
                            <?= htmlspecialchars($a['title']) ?>
                        </a>
                    </h2>

                    <p class="card-excerpt" itemprop="description">
                        <?= htmlspecialchars(excerpt($a['description'])) ?>
                    </p>

                    <div class="card-footer">
                        <span class="card-author" itemprop="author"
                              itemscope itemtype="https://schema.org/Person">
                            <span itemprop="name"><?= htmlspecialchars($a['author']['name']) ?></span>
                        </span>
                        <a class="card-read-more"
                           href="<?= htmlspecialchars(url('article', ['slug' => $a['slug']])) ?>">
                            <?= t('read_more') ?>
                        </a>
                    </div>

                    <div class="card-tags">
                        <?php foreach ($a['tags'] as $tag): ?>
                        <a href="<?= htmlspecialchars(url('tag', ['slug' => $tag])) ?>"
                           class="tag" rel="tag">
                            <?= htmlspecialchars($tag) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

            </article>
            <?php endforeach; ?>
        </div>

        <?php partial('pagination', compact('currentPage', 'totalPages')) ?>

    </main>

    <?php partial('sidebar') ?>
</div>

<?php partial('footer') ?>

</body>
</html>

