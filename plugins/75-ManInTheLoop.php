<?php

/**
 * Man in the loop — human-written articles (separate from AI-driven blog).
 *
 * - Hub ES: /man-in-the-loop  |  Hub EN: /en/man-in-the-loop
 * - Posts ES: /man-in-the-loop/{slug}  |  EN: /man-in-the-loop/en/{slug}
 * - Feed JSON: /man-in-the-loop.json  |  /en/man-in-the-loop.json
 */
class ManInTheLoop extends AbstractPicoPlugin
{
    const DEFAULT_LIMIT = 8;

    const MAX_LIMIT = 20;

    /** @var bool */
    private $jsonRoute = false;

    /** @var string es|en */
    private $jsonLang = 'es';

    public function onRequestUrl(&$url)
    {
        if ($url === 'man-in-the-loop.json') {
            $this->jsonRoute = true;
            $this->jsonLang = 'es';
        } elseif ($url === 'en/man-in-the-loop.json') {
            $this->jsonRoute = true;
            $this->jsonLang = 'en';
        }
    }

    public function onRequestFile(&$file)
    {
        if (!$this->jsonRoute) {
            return;
        }
        $pico = $this->getPico();
        $hubId = $this->jsonLang === 'en' ? 'en/man-in-the-loop' : 'man-in-the-loop';
        $hub = $pico->getConfig('content_dir') . str_replace('/', DIRECTORY_SEPARATOR, $hubId)
            . $pico->getConfig('content_ext');
        if (file_exists($hub)) {
            $file = $hub;
        }
    }

    public function onPageRendering(&$twigTemplate, array &$twigVariables)
    {
        $pico = $this->getPico();

        if ($this->jsonRoute) {
            $this->emitFeedJson($pico, $this->jsonLang);
            return;
        }

        $current = $pico->getCurrentPage();
        if ($current === null || !isset($current['id'])) {
            return;
        }

        $id = $current['id'];
        $lang = class_exists('Multilingual', false) ? Multilingual::inferLang($current) : 'es';
        $isEn = ($lang === 'en');
        $twigVariables['mitl_lang'] = $lang;
        $twigVariables['mitl_hub_url'] = $isEn ? '/en/man-in-the-loop' : '/man-in-the-loop';
        $twigVariables['mitl_json_url'] = $isEn ? '/en/man-in-the-loop.json' : '/man-in-the-loop.json';

        $allInLang = $this->collectMitlPages($pico, $lang);
        $twigVariables['mitl_sidebar_nav'] = $this->buildSidebarNav($allInLang, $pico);

        if ($id === 'man-in-the-loop' || $id === 'en/man-in-the-loop') {
            $batch = $this->collectPosts($pico, $lang, 1, self::DEFAULT_LIMIT);
            $twigVariables['mitl_initial_posts'] = $batch['posts'];
            $twigVariables['mitl_has_more'] = !empty($batch['meta']['has_more']);
            $twigVariables['mitl_page_size'] = self::DEFAULT_LIMIT;
            $twigVariables['mitl_active_slug'] = null;
            return;
        }

        if ($this->isMitlPostId($id)) {
            $slug = $this->slugFromPostId($id, $lang);
            $twigVariables['mitl_active_slug'] = $slug;
            $nav = $this->buildPostNeighbors($allInLang, $id);
            $twigVariables['mitl_post_prev'] = $nav['prev'];
            $twigVariables['mitl_post_next'] = $nav['next'];
        }
    }

    private function isMitlPostId($id)
    {
        if (strpos($id, 'man-in-the-loop/en/') === 0) {
            return true;
        }
        if (strpos($id, 'man-in-the-loop/') === 0 && strpos($id, 'man-in-the-loop/en/') !== 0) {
            return true;
        }
        return false;
    }

    private function slugFromPostId($id, $lang)
    {
        if ($lang === 'en' && strpos($id, 'man-in-the-loop/en/') === 0) {
            return substr($id, strlen('man-in-the-loop/en/'));
        }
        if (strpos($id, 'man-in-the-loop/') === 0) {
            return substr($id, strlen('man-in-the-loop/'));
        }
        return $id;
    }

    /**
     * @return array[]
     */
    private function collectMitlPages($pico, $lang)
    {
        $out = array();
        foreach ($pico->getPages() as $page) {
            if (!isset($page['id'], $page['date']) || !$page['date']) {
                continue;
            }
            $id = $page['id'];
            if (!$this->isMitlPostId($id)) {
                continue;
            }
            $pageLang = class_exists('Multilingual', false) ? Multilingual::inferLang($page) : 'es';
            if ($pageLang !== $lang) {
                continue;
            }
            $template = isset($page['meta']['template']) ? strtolower((string) $page['meta']['template']) : '';
            if ($template !== 'man-in-the-loop-post') {
                continue;
            }
            $out[] = $page;
        }

        usort($out, function ($a, $b) {
            return $b['time'] - $a['time'];
        });

        return $out;
    }

    private function buildSidebarNav(array $pages, $pico)
    {
        $baseUrl = rtrim($pico->getBaseUrl(), '/');
        $nav = array();
        foreach ($pages as $page) {
            $slug = $this->slugFromPostId(
                $page['id'],
                class_exists('Multilingual', false) ? Multilingual::inferLang($page) : 'es'
            );
            $nav[] = array(
                'slug' => $slug,
                'anchor' => 'mitl-entry-' . preg_replace('/[^a-z0-9-]+/i', '-', $slug),
                'title' => $this->readMetaString(
                    isset($page['meta']) ? $page['meta'] : array(),
                    array('title', 'Title'),
                    isset($page['title']) ? $page['title'] : ''
                ),
                'date' => isset($page['date']) ? $page['date'] : '',
                'url' => $this->absoluteUrl($page, $baseUrl),
            );
        }
        return $nav;
    }

    private function buildPostNeighbors(array $pages, $currentId)
    {
        $ids = array_column($pages, 'id');
        $pos = array_search($currentId, $ids, true);
        if ($pos === false) {
            return array('prev' => null, 'next' => null);
        }
        $baseUrl = rtrim($this->getPico()->getBaseUrl(), '/');
        $prev = $pos > 0 ? $this->serializeNavItem($pages[$pos - 1], $baseUrl) : null;
        $next = $pos < count($pages) - 1 ? $this->serializeNavItem($pages[$pos + 1], $baseUrl) : null;
        return array('prev' => $prev, 'next' => $next);
    }

    private function serializeNavItem(array $page, $baseUrl)
    {
        return array(
            'title' => $this->readMetaString(
                isset($page['meta']) ? $page['meta'] : array(),
                array('title', 'Title'),
                isset($page['title']) ? $page['title'] : ''
            ),
            'url' => $this->absoluteUrl($page, $baseUrl),
        );
    }

    /**
     * @return array{posts: array[], meta: array}
     */
    private function collectPosts($pico, $lang, $page, $limit)
    {
        $all = $this->collectMitlPages($pico, $lang);
        $page = max(1, (int) $page);
        $limit = max(1, min((int) $limit, self::MAX_LIMIT));
        $offset = ($page - 1) * $limit;
        $slice = array_slice($all, $offset, $limit);
        $baseUrl = rtrim($pico->getBaseUrl(), '/');
        $items = array();
        foreach ($slice as $p) {
            $items[] = $this->serializeFeedItem($p, $baseUrl, $lang);
        }

        return array(
            'posts' => $items,
            'meta' => array(
                'page' => $page,
                'limit' => $limit,
                'count' => count($items),
                'total' => count($all),
                'has_more' => $offset + count($slice) < count($all),
                'language' => $lang,
            ),
        );
    }

    private function serializeFeedItem(array $page, $baseUrl, $lang)
    {
        $meta = isset($page['meta']) ? $page['meta'] : array();
        $slug = $this->slugFromPostId($page['id'], $lang);
        $excerpt = $this->readMetaString($meta, array('description', 'Description'), '');
        if ($excerpt === '' && isset($page['raw_content'])) {
            $plain = trim(strip_tags($page['raw_content']));
            if (strlen($plain) > 220) {
                $excerpt = substr($plain, 0, 217) . '…';
            } else {
                $excerpt = $plain;
            }
        }

        return array(
            'slug' => $slug,
            'anchor' => 'mitl-entry-' . preg_replace('/[^a-z0-9-]+/i', '-', $slug),
            'title' => $this->readMetaString($meta, array('title', 'Title'), isset($page['title']) ? $page['title'] : ''),
            'description' => $excerpt,
            'date' => isset($page['date']) ? $page['date'] : '',
            'author' => $this->readMetaString($meta, array('author', 'Author'), ''),
            'url' => $this->absoluteUrl($page, $baseUrl),
        );
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

    private function emitFeedJson($pico, $lang)
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : self::DEFAULT_LIMIT;
        $data = $this->collectPosts($pico, $lang, $page, $limit);

        header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: public, max-age=300');

        $payload = array(
            'meta' => array_merge(
                array(
                    'generated_at' => gmdate('c'),
                    'section' => 'man-in-the-loop',
                ),
                $data['meta']
            ),
            'posts' => $data['posts'],
        );

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }
}
