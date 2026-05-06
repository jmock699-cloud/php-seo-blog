<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

// ── Slug: from $_GET (set by .htaccess) or parsed from URI ───
$slug = trim($_GET['slug'] ?? '', '/');
if (empty($slug)) {
    $uriPath  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $basePath = rtrim((string)(parse_url(SITE_URL, PHP_URL_PATH) ?? ''), '/');
    $relative = ltrim(substr($uriPath, strlen($basePath)), '/');
    if (preg_match('~^article/([^/?&]+)~', $relative, $m)) {
        $slug = rawurldecode($m[1]);
    }
}

if (empty($slug)) {
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Location: ' . url('home'), true, 302);
    exit;
}

// ── Data ──────────────────────────────────────────────────────
$article = get_article_by_slug($slug);

if (!$article) {
    http_response_code(404);
    render('404', ['pageTitle' => t('404_title'), 'message' => t('404_not_found')]);
    exit;
}

$related  = get_related_articles($slug);
$readTime = reading_time($article['content']);

// ── Build TOC ─────────────────────────────────────────────────
function build_toc(string $content): array
{
    preg_match_all('/<h([23])\s[^>]*id="([^"]+)"[^>]*>(.*?)<\/h\1>/i', $content, $m, PREG_SET_ORDER);
    if (empty($m)) {
        preg_match_all('/<h([23])>(.*?)<\/h\1>/i', $content, $m2, PREG_SET_ORDER);
        foreach ($m2 as &$item) {
            $id      = 's-' . md5($item[2]);
            $content = str_replace($item[0], "<h{$item[1]} id=\"$id\">{$item[2]}</h{$item[1]}>", $content);
            $item['auto_id'] = $id;
        }
        unset($item);
        return ['toc' => $m2, 'content' => $content];
    }
    return ['toc' => $m, 'content' => $content];
}

$tocResult    = build_toc($article['content']);
$toc          = $tocResult['toc'];
$bodyContent  = $tocResult['content'];

// ── SEO ───────────────────────────────────────────────────────
$pageTitle    = $article['title'] . ' | ' . SITE_NAME;
$pageDesc     = $article['description'];
$canonicalUrl = url('article', ['slug' => $article['slug']]);
$coverImage   = $article['cover'] ?: DEFAULT_OG_IMAGE;
$publishedIso = $article['published_at'];
$updatedIso   = $article['updated_at'];
$publishedFmt = date('Y-m-d', strtotime($publishedIso));
$updatedFmt   = date('Y-m-d', strtotime($updatedIso));
$authorUrl    = url('author', ['slug' => $article['author']['slug']]);
$categoryUrl  = url('category', ['slug' => $article['category']['slug']]);

$schema = json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'             => 'Article',
            'headline'          => $article['title'],
            'description'       => $article['description'],
            'image'             => $coverImage,
            'keywords'          => $article['tags'],
            'author'            => ['@type' => 'Person',       'name' => $article['author']['name'], 'url' => $authorUrl],
            'publisher'         => ['@type' => 'Organization', 'name' => SITE_NAME, 'logo' => ['@type' => 'ImageObject', 'url' => SITE_LOGO]],
            'datePublished'     => $publishedIso,
            'dateModified'      => $updatedIso,
            'mainEntityOfPage'  => ['@type' => 'WebPage', '@id' => $canonicalUrl],
            'timeRequired'      => 'PT' . $readTime . 'M',
        ],
        [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => t('home'),                        'item' => url('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $article['category']['name'],     'item' => $categoryUrl],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $article['title']],
            ],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

// ── Render ────────────────────────────────────────────────────
render('article', compact(
    'pageTitle', 'pageDesc', 'canonicalUrl', 'schema',
    'coverImage', 'publishedIso', 'updatedIso', 'publishedFmt', 'updatedFmt',
    'article', 'related', 'readTime', 'toc', 'bodyContent',
    'authorUrl', 'categoryUrl'
));
