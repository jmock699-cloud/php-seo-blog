<aside class="sidebar">
    <!-- Latest Articles -->
    <section class="widget">
        <h3><?= t('widget_latest') ?></h3>
        <ul>
            <?php foreach (array_slice(get_articles(1, 5)['items'], 0, 5) as $a): ?>
            <li>
                <a href="<?= htmlspecialchars(url('article', ['slug' => $a['slug']])) ?>">
                    <?= htmlspecialchars($a['title']) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <!-- Tag Cloud -->
    <section class="widget">
        <h3><?= t('widget_tags') ?></h3>
        <div class="tag-cloud">
            <?php
            $allTags = [];
            foreach (get_articles(1, 999)['items'] as $a) {
                $allTags = array_merge($allTags, $a['tags']);
            }
            foreach (array_unique($allTags) as $tg): ?>
            <a href="<?= htmlspecialchars(url('tag', ['slug' => $tg])) ?>" class="tag">
                <?= htmlspecialchars($tg) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
</aside>

