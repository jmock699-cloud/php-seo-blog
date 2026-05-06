<?php if ($totalPages > 1): ?>
<nav class="pagination" aria-label="<?= t('aria_nav_pager') ?>">
    <?php if ($currentPage > 1): ?>
    <a class="page-btn prev"
       href="<?= htmlspecialchars(url('home_paged', ['page' => $currentPage - 1])) ?>"
       rel="prev"><?= t('prev_page') ?></a>
    <?php endif; ?>

    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <a class="page-btn <?= $p === $currentPage ? 'active' : '' ?>"
       href="<?= htmlspecialchars(url('home_paged', ['page' => $p])) ?>"
       <?= $p === $currentPage ? 'aria-current="page"' : '' ?>>
        <?= $p ?>
    </a>
    <?php endfor; ?>

    <?php if ($currentPage < $totalPages): ?>
    <a class="page-btn next"
       href="<?= htmlspecialchars(url('home_paged', ['page' => $currentPage + 1])) ?>"
       rel="next"><?= t('next_page') ?></a>
    <?php endif; ?>
</nav>
<?php endif; ?>

