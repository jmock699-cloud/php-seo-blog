<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

// ── Front-controller routing ──────────────────────────────────
(function () {
    $uriPath  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $basePath = rtrim((string)(parse_url(SITE_URL, PHP_URL_PATH) ?? ''), '/');
    $relative = ltrim(substr($uriPath, strlen($basePath)), '/');

    if (empty($_GET['slug']) && preg_match('~^article/([^/?&]+)~', $relative, $m)) {
        $_GET['slug'] = rawurldecode($m[1]);
        require __DIR__ . '/article.php';
        exit;
    }
    if (empty($_GET['category']) && preg_match('~^category/([^/?&]+)~', $relative, $m)) {
        $_GET['category'] = rawurldecode($m[1]);
    }
    if (empty($_GET['tag']) && preg_match('~^tag/([^/?&]+)~', $relative, $m)) {
        $_GET['tag'] = rawurldecode($m[1]);
    }
    if (empty($_GET['author']) && preg_match('~^author/([^/?&]+)~', $relative, $m)) {
        $_GET['author'] = rawurldecode($m[1]);
    }
    if (empty($_GET['q']) && preg_match('~^search/?$~', $relative)) {
        $_GET['q'] = '';
    }
})();

function render_archive_page(string $archiveType, string $slug, int $page): void
{
    $label = match ($archiveType) {
        'category' => get_category_label($slug),
        'tag'      => get_tag_label($slug),
        'author'   => get_author_label($slug),
        default    => $slug,
    };
    $data = match ($archiveType) {
        'category' => get_articles_by_category($slug, $page),
        'tag'      => get_articles_by_tag($slug, $page),
        'author'   => get_articles_by_author($slug, $page),
    };

    $articles    = $data['items'];
    $totalPages  = $data['total_pages'];
    $total        = $data['total'];
    $currentPage = $data['page'];
    $archiveTitle = match ($archiveType) {
        'category' => t('archive_category_title', $label),
        'tag'      => t('archive_tag_title', $label),
        'author'   => t('archive_author_title', $label),
    };
    $archiveDesc = match ($archiveType) {
        'category' => t('archive_category_desc', $label, $total),
        'tag'      => t('archive_tag_desc', $label, $total),
        'author'   => t('archive_author_desc', $label, $total),
    };

    $pageTitle = $currentPage > 1
        ? t('page_title_paged', $archiveTitle . ' | ' . SITE_NAME, $currentPage)
        : $archiveTitle . ' | ' . SITE_NAME;
    $pageDesc = $currentPage > 1 ? t('page_desc_paged', $currentPage, $archiveDesc) : $archiveDesc;
    $baseArchiveUrl = url($archiveType, ['slug' => $slug]);
    $canonicalUrl = $currentPage > 1
        ? $baseArchiveUrl . (str_contains($baseArchiveUrl, '?') ? '&' : '?') . 'page=' . $currentPage
        : $baseArchiveUrl;
    $robots = $total > 0 ? 'index, follow' : 'noindex, follow';
    $prevUrl = $currentPage > 1 ? $baseArchiveUrl . (str_contains($baseArchiveUrl, '?') ? '&' : '?') . 'page=' . ($currentPage - 1) : null;
    $nextUrl = $currentPage < $totalPages ? $baseArchiveUrl . (str_contains($baseArchiveUrl, '?') ? '&' : '?') . 'page=' . ($currentPage + 1) : null;

    $schema = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => $archiveTitle,
        'description' => $archiveDesc,
        'url' => $canonicalUrl,
        'isPartOf' => ['@type' => 'WebSite', 'name' => SITE_NAME, 'url' => SITE_URL],
        'mainEntity' => [
            '@type' => 'ItemList',
            'numberOfItems' => count($articles),
            'itemListElement' => array_map(fn($a, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'url' => url('article', ['slug' => $a['slug']]),
                'name' => $a['title'],
            ], $articles, array_keys($articles)),
        ],
        'breadcrumb' => [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => t('home'), 'item' => url('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $archiveTitle, 'item' => $canonicalUrl],
            ],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

    render('archive', compact(
        'pageTitle', 'pageDesc', 'canonicalUrl', 'robots', 'prevUrl', 'nextUrl', 'schema',
        'articles', 'totalPages', 'total', 'currentPage', 'archiveType', 'archiveTitle', 'archiveDesc', 'label', 'slug', 'baseArchiveUrl'
    ));
}

// ── Data + archive/search dispatch ────────────────────────────
$page = max(1, (int)($_GET['page'] ?? 1));

if (!empty($_GET['category'])) {
    render_archive_page('category', trim((string)$_GET['category'], '/'), $page);
    exit;
}
if (!empty($_GET['tag'])) {
    render_archive_page('tag', trim((string)$_GET['tag'], '/'), $page);
    exit;
}
if (!empty($_GET['author'])) {
    render_archive_page('author', trim((string)$_GET['author'], '/'), $page);
    exit;
}
if (array_key_exists('q', $_GET)) {
    $query = trim((string)($_GET['q'] ?? ''));
    $data = search_articles($query, $page);
    $articles = $data['items'];
    $totalPages = $data['total_pages'];
    $total = $data['total'];
    $currentPage = $data['page'];
    $pageTitle = ($query !== '' ? t('search_title', $query) : t('search_empty_title')) . ' | ' . SITE_NAME;
    $pageDesc = $query !== '' ? t('search_desc', $query, $total) : t('search_empty_desc');
    $canonicalUrl = SITE_URL . '/search' . ($query !== '' ? '?q=' . rawurlencode($query) : '');
    $robots = 'noindex, follow';
    $schema = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'SearchResultsPage',
        'name' => $pageTitle,
        'description' => $pageDesc,
        'url' => $canonicalUrl,
        'isPartOf' => ['@type' => 'WebSite', 'name' => SITE_NAME, 'url' => SITE_URL],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    render('search', compact('pageTitle', 'pageDesc', 'canonicalUrl', 'robots', 'schema', 'articles', 'totalPages', 'total', 'currentPage', 'query'));
    exit;
}

// ── Home data ─────────────────────────────────────────────────
$data        = get_articles($page);
$articles    = $data['items'];
$totalPages  = $data['total_pages'];
$total        = $data['total'];
$currentPage = $data['page'];
$categories  = get_all_categories();
$tags        = get_all_tags();

// ── SEO ───────────────────────────────────────────────────────
$pageTitle    = $currentPage > 1
    ? t('page_title_paged', SITE_NAME, $currentPage)
    : SITE_NAME . ' – ' . t('latest_articles');
$pageDesc     = $currentPage > 1
    ? t('page_desc_paged', $currentPage, DEFAULT_DESCRIPTION)
    : DEFAULT_DESCRIPTION;
$canonicalUrl = url('home_paged', ['page' => $currentPage]);
$robots       = $currentPage > 1 ? 'noindex, follow' : 'index, follow';
$prevUrl      = $currentPage > 1  ? url('home_paged', ['page' => $currentPage - 1]) : null;
$nextUrl      = $currentPage < $totalPages ? url('home_paged', ['page' => $currentPage + 1]) : null;

$schema = json_encode([
    '@context'        => 'https://schema.org',
    '@graph'          => [
        [
            '@type'           => 'WebSite',
            'name'            => SITE_NAME,
            'url'             => SITE_URL,
            'inLanguage'      => SITE_LANG,
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => SITE_URL . '/search?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ],
        [
            '@type'      => 'CollectionPage',
            'name'       => $pageTitle,
            'description'=> $pageDesc,
            'url'        => $canonicalUrl,
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => count($articles),
                'itemListElement' => array_map(fn($a, $i) => [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'url' => url('article', ['slug' => $a['slug']]),
                    'name' => $a['title'],
                ], $articles, array_keys($articles)),
            ],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

// ── Render ────────────────────────────────────────────────────
render('index', compact(
    'pageTitle', 'pageDesc', 'canonicalUrl', 'robots',
    'prevUrl',   'nextUrl',  'schema',
    'articles',  'totalPages', 'currentPage', 'total', 'categories', 'tags'
));
