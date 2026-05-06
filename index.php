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
})();

// ── Data ──────────────────────────────────────────────────────
$page        = max(1, (int)($_GET['page'] ?? 1));
$data        = get_articles($page);
$articles    = $data['items'];
$totalPages  = $data['total_pages'];
$currentPage = $data['page'];

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
    '@type'           => 'WebSite',
    'name'            => SITE_NAME,
    'url'             => SITE_URL,
    'potentialAction' => [
        '@type'       => 'SearchAction',
        'target'      => SITE_URL . '/search?q={search_term_string}',
        'query-input' => 'required name=search_term_string',
    ],
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// ── Render ────────────────────────────────────────────────────
render('index', compact(
    'pageTitle', 'pageDesc', 'canonicalUrl', 'robots',
    'prevUrl',   'nextUrl',  'schema',
    'articles',  'totalPages', 'currentPage'
));
