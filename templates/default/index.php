<!DOCTYPE html>
<html lang="<?= SITE_LANG ?>">
<head>
<?php partial('head', compact('pageTitle', 'pageDesc', 'canonicalUrl', 'robots', 'prevUrl', 'nextUrl', 'schema')) ?>
</head>
<body>

<div id="reading-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>

<?php partial('header', ['activeNav' => 'home']) ?>

<section class="hero-section">
    <div class="container hero-grid">
        <div class="hero-copy">
            <p class="eyebrow"><?= t('hero_eyebrow') ?></p>
            <h1><?= htmlspecialchars(SITE_NAME) ?></h1>
            <p><?= htmlspecialchars(DEFAULT_DESCRIPTION) ?></p>
            <form class="search-form" action="<?= SITE_URL ?>/search" method="get" role="search">
                <label class="sr-only" for="site-search"><?= t('search_label') ?></label>
                <input id="site-search" type="search" name="q" placeholder="<?= htmlspecialchars(t('search_placeholder')) ?>">
                <button type="submit"><?= t('search_button') ?></button>
            </form>
        </div>
        <div class="hero-panel" aria-label="<?= t('site_stats') ?>">
            <strong><?= (int)$total ?></strong>
            <span><?= t('published_articles') ?></span>
            <strong><?= count($categories) ?></strong>
            <span><?= t('topic_categories') ?></span>
        </div>
    </div>
</section>

<div class="container main-wrap">
    <main id="main-content">
        <section id="topics" class="topic-strip" aria-label="<?= t('featured_topics') ?>">
            <h2><?= t('featured_topics') ?></h2>
            <div class="topic-links">
                <?php foreach ($categories as $category): ?>
                <a href="<?= htmlspecialchars(url('category', ['slug' => $category['slug']])) ?>">
                    <?= htmlspecialchars($category['name']) ?> <span><?= (int)$category['count'] ?></span>
                </a>
                <?php endforeach; ?>
                <?php foreach (array_slice($tags, 0, 6) as $tag): ?>
                <a href="<?= htmlspecialchars(url('tag', ['slug' => $tag['slug']])) ?>">
                    #<?= htmlspecialchars($tag['name']) ?> <span><?= (int)$tag['count'] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </section>

        <h2 class="list-title">
            <?= $currentPage > 1 ? t('latest_articles_paged', $currentPage) : t('latest_articles') ?>
        </h2>

        <div class="article-list">
            <?php foreach ($articles as $a): ?>
                <?php partial('article-card', compact('a')) ?>
            <?php endforeach; ?>
        </div>

        <?php partial('pagination', compact('currentPage', 'totalPages')) ?>
    </main>

    <?php partial('sidebar') ?>
</div>

<?php partial('footer') ?>

</body>
</html>
