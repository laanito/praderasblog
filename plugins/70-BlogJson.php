<?php

/**
 * Phase 6 — Machine-readable JSON for blog posts (agents, RAG, tooling).
 *
 * Endpoints (cache-friendly paths, no HTML chrome):
 * - GET /blog.json           — Spanish posts (content/blog/*, not blog/en/*)
 * - GET /blog/en.json        — English posts (content/blog/en/*)
 * - GET /blog/{slug}.json    — single Spanish article
 * - GET /blog/en/{slug}.json — single English article
 * - GET /search.json?q=…     — Spanish blog search (Phase 6 v1.1)
 * - GET /en/search.json?q=…  — English blog search
 * - GET /blog.json?tag=…     — listing filter by canonical tag (v1.2)
 * - GET /blog/en.json?tag=…  — English listing filter
 *
 * Schema: .agents/blog-json-api.md
 */
class BlogJson extends AbstractPicoPlugin
{
    const API_VERSION = 3;

    const SCHEMA_VERSION = '1.2';

    /** @var string|null listing-es|listing-en|post|search-es|search-en */
    private $jsonRoute = null;

    /** @var string|null Pico page id, e.g. blog/foo or blog/en/foo */
    private $jsonPostId = null;

    public function onRequestUrl(&$url)
    {
        if ($url === 'blog.json') {
            $this->jsonRoute = 'listing-es';
            return;
        }
        if ($url === 'blog/en.json') {
            $this->jsonRoute = 'listing-en';
            return;
        }
        if ($url === 'search.json') {
            $this->jsonRoute = 'search-es';
            return;
        }
        if ($url === 'en/search.json') {
            $this->jsonRoute = 'search-en';
            return;
        }
        if (preg_match('~^blog/en/([^/]+)\\.json$~', $url, $matches)) {
            $this->jsonRoute = 'post';
            $this->jsonPostId = 'blog/en/' . $matches[1];
            return;
        }
        if (preg_match('~^blog/([^/]+)\\.json$~', $url, $matches)) {
            $this->jsonRoute = 'post';
            $this->jsonPostId = 'blog/' . $matches[1];
            return;
        }

        $this->setEnabled(false);
    }

    public function onRequestFile(&$file)
    {
        if ($this->jsonRoute === null) {
            return;
        }

        $pico = $this->getPico();
        $ext = $pico->getConfig('content_ext');
        $contentDir = $pico->getConfig('content_dir');

        if ($this->jsonRoute === 'listing-es') {
            $file = $contentDir . 'blog' . $ext;
        } elseif ($this->jsonRoute === 'listing-en') {
            $file = $contentDir . 'en/blog' . $ext;
        } elseif ($this->jsonRoute === 'search-es') {
            $file = $contentDir . 'search' . $ext;
        } elseif ($this->jsonRoute === 'search-en') {
            $file = $contentDir . 'en/search' . $ext;
        } elseif ($this->jsonPostId !== null) {
            $candidate = $contentDir . $this->jsonPostId . $ext;
            if (file_exists($candidate)) {
                $file = $candidate;
            }
        }
    }

    public function onPageRendering(&$twigTemplate, array &$twigVariables)
    {
        if ($this->jsonRoute === null) {
            return;
        }

        $pico = $this->getPico();
        $pages = $pico->getPages();
        $baseUrl = rtrim($pico->getBaseUrl(), '/');
        $schemaVersion = self::SCHEMA_VERSION;
        $cacheMaxAge = 3600;
        $alternatesByKey = $this->buildAlternatesByKey($pages);

        if ($this->jsonRoute === 'search-es' || $this->jsonRoute === 'search-en') {
            $lang = ($this->jsonRoute === 'search-en') ? 'en' : 'es';
            $query = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
            if ($query === '') {
                $this->emitJsonError(400, 'Missing required query parameter: q');
            }

            $searchPlugin = $this->getSearchPlugin();
            if ($searchPlugin === null) {
                $this->emitJsonError(503, 'Search plugin unavailable');
            }

            $hits = $searchPlugin->searchBlogPosts($pages, $query, $lang);
            $items = array();
            foreach ($hits as $page) {
                $item = $this->serializeListingItem($page, $baseUrl, $alternatesByKey);
                if (isset($page['search_rank'])) {
                    $item['search_rank'] = (float) $page['search_rank'];
                }
                $items[] = $item;
            }

            $this->emitJson(
                array(
                    'meta' => array(
                        'schema_version' => $schemaVersion,
                        'generated_at' => gmdate('c'),
                        'language' => $lang,
                        'query' => $query,
                        'count' => count($items),
                    ),
                    'results' => $items,
                ),
                $cacheMaxAge
            );
        }

        if ($this->jsonRoute === 'listing-es' || $this->jsonRoute === 'listing-en') {
            $lang = ($this->jsonRoute === 'listing-en') ? 'en' : 'es';
            $tagFilter = $this->readTagFilter();
            $blogPages = $this->collectBlogPosts($pages, $lang, $tagFilter);
            $items = array();
            foreach ($blogPages as $page) {
                $items[] = $this->serializeListingItem($page, $baseUrl, $alternatesByKey);
            }

            $meta = array(
                'schema_version' => $schemaVersion,
                'generated_at' => gmdate('c'),
                'language' => $lang,
                'count' => count($items),
            );
            if ($tagFilter !== '') {
                $meta['tag_filter'] = $tagFilter;
            }

            $this->emitJson(
                array(
                    'meta' => $meta,
                    'posts' => $items,
                ),
                $cacheMaxAge
            );
        }

        if ($this->jsonRoute === 'post') {
            $page = $this->findPageById($pages, $this->jsonPostId);
            if ($page === null) {
                $this->emitJsonError(404, 'Post not found');
            }

            $lang = class_exists('Multilingual', false) ? Multilingual::inferLang($page) : 'es';
            $this->emitJson(
                array(
                    'meta' => array(
                        'schema_version' => $schemaVersion,
                        'generated_at' => gmdate('c'),
                        'language' => $lang,
                    ),
                    'post' => $this->serializePostDetail($page, $baseUrl, $alternatesByKey),
                ),
                $cacheMaxAge
            );
        }
    }

    /**
     * @param array[] $pages
     * @param string  $lang es|en
     *
     * @return array[]
     */
    private function collectBlogPosts(array $pages, $lang, $tagFilter = '')
    {
        $out = array();
        foreach ($pages as $page) {
            if (!isset($page['id'], $page['date']) || !$page['date']) {
                continue;
            }
            $id = $page['id'];
            if (strpos($id, 'blog/') !== 0) {
                continue;
            }
            if ($lang === 'es') {
                if (strpos($id, 'blog/en/') === 0) {
                    continue;
                }
            } else {
                if (strpos($id, 'blog/en/') !== 0) {
                    continue;
                }
            }
            if (class_exists('Multilingual', false) && Multilingual::inferLang($page) !== $lang) {
                continue;
            }
            if ($tagFilter !== '' && !$this->pageHasTag($page, $tagFilter)) {
                continue;
            }
            $out[] = $page;
        }

        usort($out, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $out;
    }

    /**
     * @param array[] $pages
     *
     * @return array<string, array<string, array>>
     */
    private function buildAlternatesByKey(array $pages)
    {
        $map = array();
        foreach ($pages as $page) {
            if (!isset($page['id'], $page['meta'])) {
                continue;
            }
            $key = $this->readTranslationKey($page['meta']);
            if ($key === '') {
                continue;
            }
            $lang = class_exists('Multilingual', false) ? Multilingual::inferLang($page) : 'es';
            if (!isset($map[$key])) {
                $map[$key] = array();
            }
            $map[$key][$lang] = $page;
        }
        return $map;
    }

    /**
     * @param array[] $pages
     * @param string  $id
     *
     * @return array|null
     */
    private function findPageById(array $pages, $id)
    {
        foreach ($pages as $page) {
            if (isset($page['id']) && $page['id'] === $id) {
                return $page;
            }
        }
        return null;
    }

    private function serializeListingItem(array $page, $baseUrl, array $alternatesByKey)
    {
        $meta = isset($page['meta']) ? $page['meta'] : array();
        $lang = class_exists('Multilingual', false) ? Multilingual::inferLang($page) : 'es';
        $key = $this->readTranslationKey($meta);

        return array(
            'slug' => $this->pageSlug($page),
            'id' => $page['id'],
            'title' => $this->readMetaString($meta, array('title', 'Title'), isset($page['title']) ? $page['title'] : ''),
            'description' => $this->readMetaString($meta, array('description', 'Description'), ''),
            'date' => isset($page['date']) ? $page['date'] : '',
            'author' => $this->readMetaString($meta, array('author', 'Author'), ''),
            'tags' => $this->parseTags($meta),
            'lang' => $lang,
            'translation_key' => $key !== '' ? $key : null,
            'url' => $this->absoluteUrl($page, $baseUrl),
            'alternate_url' => $this->resolveAlternateUrl($key, $lang, $alternatesByKey, $baseUrl),
            'image' => $this->readMetaString($meta, array('image', 'Image'), '') ?: null,
            'reading_time_minutes' => $this->estimateReadingMinutes($page),
            'word_count' => $this->countWords($page),
            'estimated_tokens' => $this->estimateTokens($page),
            'modified_at' => $this->pageModifiedAt($page),
        );
    }

    private function serializePostDetail(array $page, $baseUrl, array $alternatesByKey)
    {
        $item = $this->serializeListingItem($page, $baseUrl, $alternatesByKey);
        $meta = isset($page['meta']) ? $page['meta'] : array();

        $item['content'] = isset($page['raw_content']) ? $page['raw_content'] : '';
        $item['content_format'] = 'markdown';
        $item['series'] = $this->readMetaString($meta, array('series', 'Series'), '') ?: null;
        $item['series_slug'] = $this->readMetaString($meta, array('series_slug', 'Series_Slug'), '') ?: null;
        $seriesOrder = $this->readMetaString($meta, array('series_order', 'Series_Order'), '');
        $item['series_order'] = $seriesOrder !== '' ? (int) $seriesOrder : null;
        $item['modified_at'] = $this->pageModifiedAt($page);

        return $item;
    }

    private function pageSlug(array $page)
    {
        $id = isset($page['id']) ? $page['id'] : '';
        if (strpos($id, 'blog/en/') === 0) {
            return substr($id, strlen('blog/en/'));
        }
        if (strpos($id, 'blog/') === 0) {
            return substr($id, strlen('blog/'));
        }
        return $id;
    }

    private function absoluteUrl(array $page, $baseUrl)
    {
        if (isset($page['url']) && $page['url'] !== '') {
            $url = $page['url'];
            if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
                return $url;
            }
            return $baseUrl . '/' . ltrim($url, '/');
        }
        return $baseUrl;
    }

    private function resolveAlternateUrl($translationKey, $lang, array $alternatesByKey, $baseUrl)
    {
        if ($translationKey === '' || !isset($alternatesByKey[$translationKey])) {
            return null;
        }
        $pair = $alternatesByKey[$translationKey];
        $alt = null;
        if ($lang === 'es' && isset($pair['en'])) {
            $alt = $pair['en'];
        } elseif ($lang === 'en' && isset($pair['es'])) {
            $alt = $pair['es'];
        }
        if ($alt === null) {
            return null;
        }
        return $this->absoluteUrl($alt, $baseUrl);
    }

    private function readTranslationKey(array $meta)
    {
        if (isset($meta['translation_key']) && is_string($meta['translation_key'])) {
            return trim($meta['translation_key']);
        }
        if (isset($meta['Translation_Key']) && is_string($meta['Translation_Key'])) {
            return trim($meta['Translation_Key']);
        }
        return '';
    }

    private function readMetaString(array $meta, array $keys, $fallback)
    {
        foreach ($keys as $key) {
            if (isset($meta[$key]) && is_string($meta[$key]) && trim($meta[$key]) !== '') {
                return trim($meta[$key]);
            }
        }
        return is_string($fallback) ? trim($fallback) : '';
    }

    private function readTagFilter()
    {
        if (!isset($_GET['tag'])) {
            return '';
        }
        $tag = trim((string) $_GET['tag']);
        return $tag;
    }

    private function pageHasTag(array $page, $tagFilter)
    {
        $meta = isset($page['meta']) ? $page['meta'] : array();
        $tags = $this->parseTags($meta);
        return in_array($tagFilter, $tags, true);
    }

    private function parseTags(array $meta)
    {
        $raw = null;
        if (isset($meta['tags'])) {
            $raw = $meta['tags'];
        } elseif (isset($meta['Tags'])) {
            $raw = $meta['Tags'];
        }
        if ($raw === null) {
            return array();
        }
        if (is_array($raw)) {
            return array_values(array_filter(array_map('trim', $raw)));
        }
        if (!is_string($raw)) {
            return array();
        }
        $parts = preg_split('/\s*,\s*/', $raw);
        return array_values(array_filter(array_map('trim', $parts)));
    }

    private function pageBodyText(array $page)
    {
        if (isset($page['raw_content'])) {
            return $page['raw_content'];
        }
        if (isset($page['content'])) {
            return strip_tags($page['content']);
        }
        return '';
    }

    private function countWords(array $page)
    {
        $words = str_word_count($this->pageBodyText($page));
        return $words > 0 ? $words : null;
    }

    /**
     * Rough token budget for RAG chunking (~4 chars per token for Latin scripts).
     */
    private function estimateTokens(array $page)
    {
        $text = $this->pageBodyText($page);
        if ($text === '') {
            return null;
        }
        return max(1, (int) ceil(strlen($text) / 4));
    }

    private function estimateReadingMinutes(array $page)
    {
        $words = $this->countWords($page);
        if ($words === null || $words < 1) {
            return null;
        }
        return max(1, (int) round($words / 200));
    }

    /**
     * @return PicoSearch|null
     */
    private function getSearchPlugin()
    {
        if (!class_exists('PicoSearch', false)) {
            return null;
        }
        $pico = $this->getPico();
        try {
            $plugin = $pico->getPlugin('PicoSearch');
            if ($plugin instanceof PicoSearch) {
                return $plugin;
            }
        } catch (RuntimeException $e) {
            // Plugin not registered; fall back to a dedicated instance.
        }
        return new PicoSearch($pico);
    }

    private function pageModifiedAt(array $page)
    {
        if (!empty($page['modificationTime']) && is_int($page['modificationTime'])) {
            return gmdate('c', $page['modificationTime']);
        }
        $pico = $this->getPico();
        $path = $pico->getConfig('content_dir') . $page['id'] . $pico->getConfig('content_ext');
        if (isset($page['id']) && file_exists($path)) {
            return gmdate('c', filemtime($path));
        }
        return null;
    }

    private function jsonEncodeFlags()
    {
        $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
        if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
            $flags |= JSON_INVALID_UTF8_SUBSTITUTE;
        }
        return $flags;
    }

    private function emitJson(array $payload, $cacheMaxAge)
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
        header('Content-Type: application/json; charset=utf-8');
        if ($cacheMaxAge > 0) {
            header('Cache-Control: public, max-age=' . (int) $cacheMaxAge);
        }
        $json = json_encode($payload, $this->jsonEncodeFlags());
        if ($json === false) {
            $this->emitJsonError(500, 'JSON encoding failed');
        }
        echo $json;
        exit;
    }

    private function emitJsonError($statusCode, $message)
    {
        $code = (int) $statusCode;
        header($_SERVER['SERVER_PROTOCOL'] . ' ' . $code);
        header('Content-Type: application/json; charset=utf-8');
        $json = json_encode(
            array(
                'error' => $message,
                'status' => $code,
            ),
            $this->jsonEncodeFlags() & ~JSON_PRETTY_PRINT
        );
        if ($json === false) {
            echo '{"error":"JSON encoding failed","status":500}';
        } else {
            echo $json;
        }
        exit;
    }
}
