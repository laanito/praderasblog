<?php

/**
 * Man in the loop — human-written articles (separate from AI-driven blog).
 *
 * - Hub: /man-in-the-loop (Template: man-in-the-loop-feed)
 * - Posts: /man-in-the-loop/{slug} (Template: man-in-the-loop-post)
 * - Infinite scroll feed: GET /man-in-the-loop.json?page=1&limit=8
 *
 * Not included in blog search, series, archive, tags, or /blog.json.
 */
class ManInTheLoop extends AbstractPicoPlugin
{
    const DEFAULT_LIMIT = 8;

    const MAX_LIMIT = 20;

    /** @var string|null */
    private $jsonRoute = false;

    public function onRequestUrl(&$url)
    {
        if ($url === 'man-in-the-loop.json') {
            $this->jsonRoute = true;
        }
    }

    public function onRequestFile(&$file)
    {
        if (!$this->jsonRoute) {
            return;
        }
        $pico = $this->getPico();
        $hub = $pico->getConfig('content_dir') . 'man-in-the-loop' . $pico->getConfig('content_ext');
        if (file_exists($hub)) {
            $file = $hub;
        }
    }

    public function onPageRendering(&$twigTemplate, array &$twigVariables)
    {
        $pico = $this->getPico();

        if ($this->jsonRoute) {
            $this->emitFeedJson($pico);
            return;
        }

        $current = $pico->getCurrentPage();
        if ($current === null || !isset($current['id'])) {
            return;
        }

        $id = $current['id'];
        if ($id === 'man-in-the-loop') {
            $batch = $this->collectPosts($pico, 1, self::DEFAULT_LIMIT);
            $twigVariables['mitl_initial_posts'] = $batch['posts'];
            $twigVariables['mitl_has_more'] = !empty($batch['meta']['has_more']);
            $twigVariables['mitl_page_size'] = self::DEFAULT_LIMIT;
            return;
        }

        if (strpos($id, 'man-in-the-loop/') === 0) {
            $twigVariables['mitl_hub_url'] = rtrim($pico->getBaseUrl(), '/') . '/man-in-the-loop';
        }
    }

    /**
     * @return array[]
     */
    private function collectMitlPages($pico)
    {
        $out = array();
        foreach ($pico->getPages() as $page) {
            if (!isset($page['id'], $page['date']) || !$page['date']) {
                continue;
            }
            if (strpos($page['id'], 'man-in-the-loop/') !== 0) {
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

    /**
     * @return array{posts: array[], meta: array}
     */
    private function collectPosts($pico, $page, $limit)
    {
        $all = $this->collectMitlPages($pico);
        $page = max(1, (int) $page);
        $limit = max(1, min((int) $limit, self::MAX_LIMIT));
        $offset = ($page - 1) * $limit;
        $slice = array_slice($all, $offset, $limit);
        $baseUrl = rtrim($pico->getBaseUrl(), '/');
        $items = array();
        foreach ($slice as $p) {
            $items[] = $this->serializeFeedItem($p, $baseUrl);
        }

        return array(
            'posts' => $items,
            'meta' => array(
                'page' => $page,
                'limit' => $limit,
                'count' => count($items),
                'total' => count($all),
                'has_more' => $offset + count($slice) < count($all),
            ),
        );
    }

    private function serializeFeedItem(array $page, $baseUrl)
    {
        $meta = isset($page['meta']) ? $page['meta'] : array();
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
            'slug' => substr($page['id'], strlen('man-in-the-loop/')),
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

    private function emitFeedJson($pico)
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : self::DEFAULT_LIMIT;
        $data = $this->collectPosts($pico, $page, $limit);

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
