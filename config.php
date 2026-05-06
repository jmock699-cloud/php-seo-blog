<?php
declare(strict_types=1);

// ═══════════════════════════════════════════════════════════════
//  Global Configuration
// ═══════════════════════════════════════════════════════════════

// ── ① Pseudo-static toggle ────────────────────────────────────
//   true  → Clean URLs  :  /article/slug   /?page=2
//   false → Query URLs  :  /article.php?slug=slug   /?page=2
//   NOTE: When true, ensure .htaccess / Nginx rewrite rules are active.
define('PSEUDO_STATIC', true);

// ── Active template set ───────────────────────────────────────
// Change to any folder name under templates/ to swap the whole look.
define('ACTIVE_TEMPLATE', 'default');

// ── ② Site basics ─────────────────────────────────────────────
define('SITE_URL',         'http://127.0.0.1/article-seo-one');  // No trailing slash
define('SITE_LOGO',        SITE_URL . '/assets/logo.png');
define('SITE_TIMEZONE',    'Asia/Shanghai');
define('DEFAULT_OG_IMAGE', SITE_URL . '/assets/default-cover.jpg');
define('PER_PAGE', 10);

// ── ③ Multi-language ──────────────────────────────────────────
define('DEFAULT_LANG',    'en');
define('AVAILABLE_LANGS', ['en', 'zh', 'es', 'de', 'fr', 'it', 'pt', 'ja', 'ko', 'ru']);

// HTML lang + OG locale map
define('LANG_META', [
    'en' => ['html' => 'en',    'locale' => 'en_US'],
    'zh' => ['html' => 'zh-CN', 'locale' => 'zh_CN'],
    'es' => ['html' => 'es',    'locale' => 'es_ES'],
    'de' => ['html' => 'de',    'locale' => 'de_DE'],
    'fr' => ['html' => 'fr',    'locale' => 'fr_FR'],
    'it' => ['html' => 'it',    'locale' => 'it_IT'],
    'pt' => ['html' => 'pt',    'locale' => 'pt_BR'],
    'ja' => ['html' => 'ja',    'locale' => 'ja_JP'],
    'ko' => ['html' => 'ko',    'locale' => 'ko_KR'],
    'ru' => ['html' => 'ru',    'locale' => 'ru_RU'],
]);

/**
 * Detect active language:  ?lang= > cookie > browser Accept-Language > default
 */
function detect_lang(): string
{
    // Priority 1: explicit ?lang= param → overwrite cookie (including reset to default)
    $lang = $_GET['lang'] ?? null;
    if ($lang && in_array($lang, AVAILABLE_LANGS, true)) {
        setcookie('site_lang', $lang, time() + 86400 * 365, '/');
        $_COOKIE['site_lang'] = $lang;   // visible within this request too
        return $lang;
    }
    // Priority 2: cookie
    if (!empty($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], AVAILABLE_LANGS, true)) {
        return $_COOKIE['site_lang'];
    }
    // Priority 3: browser Accept-Language
    $accept = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
    foreach (AVAILABLE_LANGS as $l) {
        if (str_starts_with($accept, $l) || str_contains($accept, ',' . $l) || str_contains($accept, ';' . $l)) {
            return $l;
        }
    }
    return DEFAULT_LANG;
}

$GLOBALS['_lang'] = detect_lang();

$_langFile        = __DIR__ . '/lang/' . $GLOBALS['_lang'] . '.php';
$GLOBALS['_i18n'] = file_exists($_langFile)
    ? require $_langFile
    : require __DIR__ . '/lang/' . DEFAULT_LANG . '.php';

/**
 * Translate a UI string key, with optional sprintf placeholders.
 *   t('min_read', 5)  →  "5 min read"
 */
function t(string $key, mixed ...$args): string
{
    $str = $GLOBALS['_i18n'][$key] ?? $key;
    return $args ? sprintf($str, ...$args) : $str;
}

// Language-dependent constants (must come after t() is defined)
define('SITE_NAME',           t('site_name'));
$_lm = LANG_META[$GLOBALS['_lang']] ?? LANG_META['en'];
define('SITE_LANG',           $_lm['html']);
define('SITE_LOCALE',         $_lm['locale']);
define('DEFAULT_DESCRIPTION', t('site_desc'));

// ── ④ URL helper ──────────────────────────────────────────────
/**
 * Build an internal URL respecting PSEUDO_STATIC.
 *
 * $type:   'home' | 'home_paged' | 'article' | 'category' | 'tag' | 'author'
 * $params: ['page'=>n]  or  ['slug'=>'...']
 */
function url(string $type, array $params = []): string
{
    $base      = SITE_URL;
    $langParam = ($GLOBALS['_lang'] !== DEFAULT_LANG) ? ['lang' => $GLOBALS['_lang']] : [];

    if (PSEUDO_STATIC) {
        switch ($type) {
            case 'home':
                $qs = http_build_query($langParam);
                return $base . '/' . ($qs ? '?' . $qs : '');

            case 'home_paged':
                $p = (int)($params['page'] ?? 1);
                if ($p <= 1) {
                    $qs = http_build_query($langParam);
                    return $base . '/' . ($qs ? '?' . $qs : '');
                }
                $qs = http_build_query(array_merge($langParam, ['page' => $p]));
                return $base . '/?' . $qs;

            case 'article':
                $qs = http_build_query($langParam);
                return $base . '/article/' . rawurlencode($params['slug'] ?? '')
                    . ($qs ? '?' . $qs : '');

            case 'category':
                $qs = http_build_query($langParam);
                return $base . '/category/' . rawurlencode($params['slug'] ?? '')
                    . ($qs ? '?' . $qs : '');

            case 'tag':
                $qs = http_build_query($langParam);
                return $base . '/tag/' . rawurlencode($params['slug'] ?? '')
                    . ($qs ? '?' . $qs : '');

            case 'author':
                $qs = http_build_query($langParam);
                return $base . '/author/' . rawurlencode($params['slug'] ?? '')
                    . ($qs ? '?' . $qs : '');
        }
    } else {
        switch ($type) {
            case 'home':
                $qs = http_build_query($langParam);
                return $base . '/' . ($qs ? '?' . $qs : '');

            case 'home_paged':
                $p        = (int)($params['page'] ?? 1);
                $combined = $p > 1 ? array_merge($langParam, ['page' => $p]) : $langParam;
                $qs       = http_build_query($combined);
                return $base . '/' . ($qs ? '?' . $qs : '');

            case 'article':
                $qs = http_build_query(array_merge($langParam, ['slug' => $params['slug'] ?? '']));
                return $base . '/article.php?' . $qs;

            case 'category':
                $qs = http_build_query(array_merge($langParam, ['category' => $params['slug'] ?? '']));
                return $base . '/?' . $qs;

            case 'tag':
                $qs = http_build_query(array_merge($langParam, ['tag' => $params['slug'] ?? '']));
                return $base . '/?' . $qs;

            case 'author':
                $qs = http_build_query(array_merge($langParam, ['author' => $params['slug'] ?? '']));
                return $base . '/?' . $qs;
        }
    }
    return $base . '/';
}

/**
 * Build a URL that switches to $newLang while keeping the current path + query.
 * Always includes ?lang= so detect_lang() can overwrite the cookie correctly.
 * (For the default lang the param is still passed on the switch link itself;
 *  internal navigation via url() strips it again once the cookie is updated.)
 */
function lang_url(string $newLang): string
{
    $params         = $_GET;
    $params['lang'] = $newLang;          // always set, even for default lang

    $qs     = http_build_query($params);
    $path   = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $subDir = rtrim((string)(parse_url(SITE_URL, PHP_URL_PATH) ?? ''), '/');
    if ($subDir !== '' && str_starts_with($path, $subDir)) {
        $path = substr($path, strlen($subDir));
    }
    if ($path === '' || $path === false) {
        $path = '/';
    }
    return SITE_URL . $path . ($qs ? '?' . $qs : '');
}

// ── ⑤ Article helpers ─────────────────────────────────────────

/** Resolve a multilingual field (array keyed by lang code) or return as-is. */
function lang_field(mixed $value): string
{
    if (is_array($value)) {
        $lang = $GLOBALS['_lang'];
        return (string)($value[$lang] ?? $value[DEFAULT_LANG] ?? reset($value));
    }
    return (string)$value;
}

/** Flatten a raw article record to single-language strings. */
function normalize_article(array $a): array
{
    foreach (['title', 'description', 'keywords', 'content'] as $f) {
        if (isset($a[$f])) {
            $a[$f] = lang_field($a[$f]);
        }
    }
    if (isset($a['category']['name'])) {
        $a['category']['name'] = lang_field($a['category']['name']);
    }
    if (isset($a['tags']) && is_array($a['tags'])) {
        $a['tags'] = array_map('lang_field', $a['tags']);
    }
    if (isset($a['author']['name'])) {
        $a['author']['name'] = lang_field($a['author']['name']);
    }
    return $a;
}

// ── ⑥ Data access ─────────────────────────────────────────────

function get_articles(int $page = 1, int $perPage = PER_PAGE): array
{
    $all   = array_map('normalize_article', _all_articles());
    $total = count($all);
    return [
        'items'       => array_slice($all, ($page - 1) * $perPage, $perPage),
        'total'       => $total,
        'page'        => $page,
        'per_page'    => $perPage,
        'total_pages' => (int)ceil($total / $perPage),
    ];
}

function get_article_by_slug(string $slug): ?array
{
    foreach (_all_articles() as $a) {
        if ($a['slug'] === $slug) {
            return normalize_article($a);
        }
    }
    return null;
}

function get_related_articles(string $slug, int $limit = 3): array
{
    $all = array_filter(_all_articles(), fn($a) => $a['slug'] !== $slug);
    return array_map('normalize_article', array_slice(array_values($all), 0, $limit));
}

/** Estimate reading time (~300 chars/min). */
function reading_time(string $content): int
{
    $text = strip_tags($content);
    return max(1, (int)ceil(mb_strlen($text) / 300));
}

/** Plain-text excerpt. */
function excerpt(string $content, int $len = 120): string
{
    $text = strip_tags($content);
    return mb_strlen($text) > $len ? mb_substr($text, 0, $len) . '…' : $text;
}

// ── ⑦ Sample data (bilingual) ─────────────────────────────────
function _all_articles(): array
{
    return [
        [
            'id'          => 1,
            'slug'        => 'php-seo-best-practices',
            'title'       => [
                'en' => 'Complete Guide to PHP Website SEO Best Practices',
                'zh' => 'PHP 网站 SEO 最佳实践完整指南',
            ],
            'description' => [
                'en' => 'A detailed guide to core PHP website SEO factors: meta tags, structured data, performance optimisation, and practical tips to rank higher in search results.',
                'zh' => '本文详细介绍 PHP 网站 SEO 优化的核心要素，包括 meta 标签、结构化数据、性能优化等实用技巧，帮助你的网站获得更好的搜索排名。',
            ],
            'keywords'    => [
                'en' => 'PHP SEO, website optimisation, structured data, meta tags',
                'zh' => 'PHP SEO, 网站优化, 结构化数据, meta标签',
            ],
            'cover'       => DEFAULT_OG_IMAGE,
            'category'    => [
                'name' => ['en' => 'Technology', 'zh' => '技术'],
                'slug' => 'tech',
            ],
            'tags'  => [
                ['en' => 'PHP',              'zh' => 'PHP'],
                ['en' => 'SEO',              'zh' => 'SEO'],
                ['en' => 'Web Optimisation', 'zh' => '网站优化'],
            ],
            'author'      => ['name' => ['en' => 'Zhang San', 'zh' => '张三'], 'slug' => 'zhangsan'],
            'published_at'=> '2026-05-01T08:00:00+08:00',
            'updated_at'  => '2026-05-06T10:00:00+08:00',
            'content'     => [
                'en' => '<h2 id="s1">What is SEO</h2><p>Search Engine Optimisation (SEO) is the practice of improving a website\'s visibility in organic search results through technical and content strategies.</p><h2 id="s2">Essential Meta Tags</h2><p>Title, description, and canonical tags are critical signals for search engine crawlers.</p><h2 id="s3">Structured Data</h2><p>Schema.org JSON-LD markup tells search engines what your content means and can unlock rich results.</p>',
                'zh' => '<h2 id="s1">什么是 SEO</h2><p>搜索引擎优化（SEO）是通过技术与内容手段，提升网站在搜索结果页（SERP）中的自然排名。</p><h2 id="s2">核心 meta 标签</h2><p>包括 title、description、canonical 等，对搜索引擎爬取至关重要。</p><h2 id="s3">结构化数据</h2><p>通过 Schema.org JSON-LD 告知搜索引擎内容语义，可获得富媒体摘要。</p>',
            ],
        ],
        [
            'id'          => 2,
            'slug'        => 'open-graph-guide',
            'title'       => [
                'en' => 'Open Graph Protocol Explained: Make Social Shares Look Great',
                'zh' => 'Open Graph 协议详解：让社交分享更美观',
            ],
            'description' => [
                'en' => 'A deep dive into every Open Graph attribute and how to dynamically generate OG tags in PHP so your articles show beautiful cards on Facebook, Twitter, and WeChat.',
                'zh' => '深入讲解 Open Graph 协议的每一个属性，以及如何在 PHP 中动态生成 OG 标签，让文章在微信、Facebook 等平台分享时显示精美卡片。',
            ],
            'keywords'    => [
                'en' => 'Open Graph, OG tags, social sharing, PHP',
                'zh' => 'Open Graph, OG标签, 社交分享, PHP',
            ],
            'cover'       => DEFAULT_OG_IMAGE,
            'category'    => [
                'name' => ['en' => 'Technology', 'zh' => '技术'],
                'slug' => 'tech',
            ],
            'tags'  => [
                ['en' => 'Open Graph',   'zh' => 'Open Graph'],
                ['en' => 'SEO',          'zh' => 'SEO'],
                ['en' => 'Social Media', 'zh' => '社交媒体'],
            ],
            'author'      => ['name' => ['en' => 'Li Si', 'zh' => '李四'], 'slug' => 'lisi'],
            'published_at'=> '2026-05-03T09:00:00+08:00',
            'updated_at'  => '2026-05-03T09:00:00+08:00',
            'content'     => [
                'en' => '<h2 id="s1">What is Open Graph</h2><p>Open Graph is a protocol introduced by Facebook that uses meta tags to declare how a web page should appear when shared on social networks.</p><h2 id="s2">Required Properties</h2><p>og:title, og:type, og:image, and og:url are the four essential properties every page must include.</p>',
                'zh' => '<h2 id="s1">什么是 Open Graph</h2><p>Open Graph 是 Facebook 提出的一种协议，通过 meta 标签声明网页的社交分享信息。</p><h2 id="s2">必填属性</h2><p>og:title、og:type、og:image、og:url 是必须提供的四个基础属性。</p>',
            ],
        ],
        [
            'id'          => 3,
            'slug'        => 'schema-structured-data',
            'title'       => [
                'en' => 'Schema.org Structured Data in Practice: Unlock Rich Results',
                'zh' => 'Schema.org 结构化数据实战：获取搜索富摘要',
            ],
            'description' => [
                'en' => 'Step-by-step guide to implementing Article, BreadcrumbList, and FAQ structured data with JSON-LD, passing Google Rich Results tests, and boosting click-through rates.',
                'zh' => '手把手教你用 JSON-LD 实现 Article、BreadcrumbList、FAQ 等结构化数据，通过 Google 富媒体测试，提升点击率。',
            ],
            'keywords'    => [
                'en' => 'Schema.org, JSON-LD, structured data, rich results',
                'zh' => 'Schema.org, JSON-LD, 结构化数据, 富摘要',
            ],
            'cover'       => DEFAULT_OG_IMAGE,
            'category'    => [
                'name' => ['en' => 'Technology', 'zh' => '技术'],
                'slug' => 'tech',
            ],
            'tags'  => [
                ['en' => 'Schema.org', 'zh' => 'Schema.org'],
                ['en' => 'SEO',        'zh' => 'SEO'],
                ['en' => 'JSON-LD',    'zh' => 'JSON-LD'],
            ],
            'author'      => ['name' => ['en' => 'Wang Wu', 'zh' => '王五'], 'slug' => 'wangwu'],
            'published_at'=> '2026-05-05T08:30:00+08:00',
            'updated_at'  => '2026-05-06T08:00:00+08:00',
            'content'     => [
                'en' => '<h2 id="s1">Why Structured Data Matters</h2><p>Structured data lets Google understand the semantic meaning of your content, enabling rich results like star ratings, FAQs, and breadcrumbs in the SERPs.</p>',
                'zh' => '<h2 id="s1">为什么需要结构化数据</h2><p>结构化数据能让 Google 理解页面内容语义，从而在搜索结果中展示评分、FAQ、面包屑等富媒体摘要。</p>',
            ],
        ],
    ];
}

date_default_timezone_set(SITE_TIMEZONE);

// ── ⑧ Template engine ─────────────────────────────────────────

/**
 * Render a template file, injecting $vars into its scope.
 *
 * render('index', ['articles' => $list, ...])
 * → loads templates/{ACTIVE_TEMPLATE}/index.php
 */
function render(string $tpl, array $vars = []): void
{
    $file = __DIR__ . '/templates/' . ACTIVE_TEMPLATE . '/' . $tpl . '.php';
    if (!file_exists($file)) {
        http_response_code(500);
        die('Template not found: ' . htmlspecialchars($tpl));
    }
    extract($vars, EXTR_SKIP);
    require $file;
}

/**
 * Render a partial template from templates/{ACTIVE_TEMPLATE}/partials/.
 *
 * partial('header')
 * partial('head', ['title' => '...', ...])
 */
function partial(string $name, array $vars = []): void
{
    render('partials/' . $name, $vars);
}


