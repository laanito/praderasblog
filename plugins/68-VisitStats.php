<?php

/**
 * Poor-man visit stats backed by Redis (127.0.0.1:6379 by default).
 *
 * Counts HTML page views and JSON API hits (blog.json, search.json, MITL feeds, etc.).
 * Public report: /estadisticas (ES) · /en/stats (EN) · GET /stats.json
 *
 * Lazy disk persistence: first counted hit each UTC day writes var/visit-stats/latest.json
 * (and daily/YYYY-MM-DD.json on rollover). Restores from latest.json if Redis is empty.
 *
 * Loads before 70-BlogJson so JSON routes are counted before those handlers exit().
 */
class VisitStats extends AbstractPicoPlugin
{
    const SCHEMA_VERSION = '1.0';

    /** @var array<string, mixed> */
    private $config = array();

    /** @var string|null html|json */
    private $routeKind = null;

    /** @var string|null normalized path key for ZINCRBY */
    private $routePath = null;

    /** @var string|null Pico page id when applicable */
    private $routePageId = null;

    /** @var bool */
    private $emitStatsJson = false;

    /** @var Redis|null */
    private $redis = null;

    /** @var bool */
    private $redisTried = false;

    public function onConfigLoaded(array &$config)
    {
        $defaults = array(
            'enabled' => true,
            'redis_host' => '127.0.0.1',
            'redis_port' => 6379,
            'redis_timeout' => 0.5,
            'redis_password' => null,
            'redis_prefix' => 'praderas:stats',
            'skip_bots' => true,
            'exclude_self' => true,
            'exclude_page_ids' => array('estadisticas', 'en/stats'),
            'skip_paths' => array(
                'robots.txt',
                'sitemap.xml',
                'sitemap-es.xml',
                'sitemap-en.xml',
            ),
            'disk_save' => true,
            'disk_dir' => 'var/visit-stats',
        );

        if (isset($config['VisitStats']) && is_array($config['VisitStats'])) {
            $this->config = array_merge($defaults, $config['VisitStats']);
        } else {
            $this->config = $defaults;
        }
    }

    public function onRequestUrl(&$url)
    {
        if (!$this->isStatsEnabled()) {
            return;
        }

        if ($url === 'stats.json') {
            $this->emitStatsJson = true;
            $this->routeKind = 'json';
            $this->routePath = '/stats.json';
            return;
        }

        $classified = $this->classifyUrl($url);
        if ($classified !== null) {
            $this->routeKind = $classified['kind'];
            $this->routePath = $classified['path'];
            if (isset($classified['page_id'])) {
                $this->routePageId = $classified['page_id'];
            }
        }
    }

    public function onRequestFile(&$file)
    {
        if (!$this->emitStatsJson) {
            return;
        }

        $pico = $this->getPico();
        $candidate = $pico->getConfig('content_dir') . 'estadisticas' . $pico->getConfig('content_ext');
        if (file_exists($candidate)) {
            $file = $candidate;
        }
    }

    public function onPageRendering(&$twigTemplate, array &$twigVariables)
    {
        if (!$this->isStatsEnabled()) {
            return;
        }

        $pico = $this->getPico();
        $current = $pico->getCurrentPage();

        if ($current !== null && isset($current['id'])) {
            if ($this->routeKind === null) {
                $this->routeKind = 'html';
                $this->routePath = $current['id'];
                $this->routePageId = $current['id'];
            } elseif ($this->routePageId === null) {
                $this->routePageId = $current['id'];
            }
        }

        if ($this->shouldCountHit($current)) {
            $this->recordHit($this->routeKind, $this->routePath, $this->routePageId);
        }

        if ($this->emitStatsJson) {
            $this->emitPublicStatsJson($pico);
        }

        $this->attachReadOnlyContext($pico, $twigVariables, $current);
    }

    private function isStatsEnabled()
    {
        return !empty($this->config['enabled']);
    }

    /**
     * @return array{kind: string, path: string, page_id?: string}|null
     */
    private function classifyUrl($url)
    {
        $url = trim((string) $url, '/');
        if ($url === '') {
            return null;
        }

        $skip = isset($this->config['skip_paths']) ? (array) $this->config['skip_paths'] : array();
        if (in_array($url, $skip, true)) {
            return null;
        }

        $jsonExact = array(
            'blog.json' => '/blog.json',
            'blog/en.json' => '/blog/en.json',
            'search.json' => '/search.json',
            'en/search.json' => '/en/search.json',
            'man-in-the-loop.json' => '/man-in-the-loop.json',
            'en/man-in-the-loop.json' => '/en/man-in-the-loop.json',
        );
        if (isset($jsonExact[$url])) {
            return array('kind' => 'json', 'path' => $jsonExact[$url]);
        }

        if (preg_match('~^blog/en/([^/]+)\\.json$~', $url, $matches)) {
            return array(
                'kind' => 'json',
                'path' => '/blog/en/' . $matches[1] . '.json',
                'page_id' => 'blog/en/' . $matches[1],
            );
        }
        if (preg_match('~^blog/([^/]+)\\.json$~', $url, $matches)) {
            return array(
                'kind' => 'json',
                'path' => '/blog/' . $matches[1] . '.json',
                'page_id' => 'blog/' . $matches[1],
            );
        }

        return null;
    }

    private function shouldCountHit($currentPage)
    {
        if ($this->routeKind === null || $this->routePath === null || $this->routePath === '') {
            return false;
        }

        if (!empty($this->config['skip_bots']) && $this->isBotUserAgent()) {
            return false;
        }

        if (!empty($this->config['exclude_self'])
            && $this->routeKind === 'html'
            && $currentPage !== null
            && isset($currentPage['id'])
        ) {
            $exclude = isset($this->config['exclude_page_ids']) ? (array) $this->config['exclude_page_ids'] : array();
            if (in_array($currentPage['id'], $exclude, true)) {
                return false;
            }
        }

        return $this->getRedis() !== null;
    }

    private function isBotUserAgent()
    {
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower((string) $_SERVER['HTTP_USER_AGENT']) : '';
        if ($ua === '') {
            return false;
        }

        $needles = array(
            'bot', 'spider', 'crawl', 'slurp', 'preview', 'facebookexternalhit',
            'embedly', 'quora link preview', 'monitor', 'wget', 'curl/', 'python-requests',
            'go-http-client', 'gptbot', 'claudebot', 'anthropic-ai',
        );
        foreach ($needles as $needle) {
            if (strpos($ua, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    private function recordHit($kind, $pathKey, $pageId = null)
    {
        $redis = $this->getRedis();
        if ($redis === null || $pathKey === null || $pathKey === '') {
            return;
        }

        $prefix = rtrim((string) $this->config['redis_prefix'], ':');
        $day = gmdate('Ymd');

        $this->maybeLazyDiskSave($redis, $prefix, $day);

        try {
            $redis->multi(Redis::PIPELINE);
            $redis->incr($prefix . ':total');
            $redis->incr($prefix . ':' . $kind);
            $redis->zIncrBy($prefix . ':paths:' . $kind, 1, $pathKey);
            $redis->hIncrBy($prefix . ':days', $day . ':total', 1);
            $redis->hIncrBy($prefix . ':days', $day . ':' . $kind, 1);
            if (is_string($pageId) && $pageId !== '' && strpos($pageId, 'blog/') === 0) {
                $redis->hIncrBy($prefix . ':pages', $pageId, 1);
            }
            $redis->exec();
        } catch (Exception $e) {
            $this->redis = null;
        }
    }

    private function getDiskDir()
    {
        $rel = isset($this->config['disk_dir']) ? (string) $this->config['disk_dir'] : 'var/visit-stats';
        $rel = trim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $rel), DIRECTORY_SEPARATOR);
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . $rel;
    }

    /**
     * At most one disk write per UTC day, on the first counted hit of that day (before increment).
     * On day rollover, archives the prior calendar day's Redis snapshot.
     */
    private function maybeLazyDiskSave(Redis $redis, $prefix, $today)
    {
        if (empty($this->config['disk_save'])) {
            return;
        }

        $dir = $this->getDiskDir();
        if (!$this->ensureDiskDir($dir)) {
            return;
        }

        $markerPath = $dir . DIRECTORY_SEPARATOR . '.snapshot-day';
        $lastDay = file_exists($markerPath) ? trim((string) file_get_contents($markerPath)) : '';

        if ($lastDay === $today) {
            return;
        }

        $lockPath = $dir . DIRECTORY_SEPARATOR . '.snapshot.lock';
        $lock = @fopen($lockPath, 'c');
        if ($lock === false || !flock($lock, LOCK_EX | LOCK_NB)) {
            if ($lock !== false) {
                fclose($lock);
            }
            return;
        }

        try {
            $lastDay = file_exists($markerPath) ? trim((string) file_get_contents($markerPath)) : '';
            if ($lastDay === $today) {
                return;
            }

            $payload = $this->exportRedisSnapshot($redis, $prefix);
            $payload['meta'] = array(
                'saved_at' => gmdate('c'),
                'schema' => 'visit-stats-disk-1',
                'snapshot_day' => $today,
                'previous_marker_day' => $lastDay !== '' ? $lastDay : null,
            );

            $this->writeJsonFile($dir . DIRECTORY_SEPARATOR . 'latest.json', $payload);

            if ($lastDay !== '') {
                $dailyDir = $dir . DIRECTORY_SEPARATOR . 'daily';
                $this->ensureDiskDir($dailyDir);
                $dailyName = substr($lastDay, 0, 4) . '-'
                    . substr($lastDay, 4, 2) . '-'
                    . substr($lastDay, 6, 2);
                $this->writeJsonFile($dailyDir . DIRECTORY_SEPARATOR . $dailyName . '.json', $payload);
            }

            file_put_contents($markerPath, $today, LOCK_EX);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function ensureDiskDir($dir)
    {
        if (is_dir($dir)) {
            return is_writable($dir);
        }
        return @mkdir($dir, 0755, true) && is_writable($dir);
    }

    private function writeJsonFile($path, array $payload)
    {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        if ($json === false) {
            return false;
        }

        $tmp = $path . '.tmp.' . getmypid();
        if (file_put_contents($tmp, $json, LOCK_EX) === false) {
            @unlink($tmp);
            return false;
        }

        return @rename($tmp, $path);
    }

    /**
     * @return array<string, mixed>
     */
    private function exportRedisSnapshot(Redis $redis, $prefix)
    {
        $pathsHtml = $redis->zRange($prefix . ':paths:html', 0, -1, true);
        $pathsJson = $redis->zRange($prefix . ':paths:json', 0, -1, true);
        $days = $redis->hGetAll($prefix . ':days');
        $pages = $redis->hGetAll($prefix . ':pages');

        return array(
            'totals' => array(
                'all' => (int) $redis->get($prefix . ':total'),
                'html' => (int) $redis->get($prefix . ':html'),
                'json' => (int) $redis->get($prefix . ':json'),
            ),
            'days' => is_array($days) ? $days : array(),
            'paths_html' => is_array($pathsHtml) ? $pathsHtml : array(),
            'paths_json' => is_array($pathsJson) ? $pathsJson : array(),
            'pages' => is_array($pages) ? $pages : array(),
        );
    }

    private function tryRestoreFromDisk(Redis $redis, $prefix)
    {
        if (empty($this->config['disk_save'])) {
            return;
        }

        try {
            if ($redis->exists($prefix . ':total')) {
                return;
            }
        } catch (Exception $e) {
            return;
        }

        $latestPath = $this->getDiskDir() . DIRECTORY_SEPARATOR . 'latest.json';
        if (!is_readable($latestPath)) {
            return;
        }

        $raw = file_get_contents($latestPath);
        if ($raw === false || $raw === '') {
            return;
        }

        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            return;
        }

        $this->importRedisSnapshot($redis, $prefix, $payload);
    }

    private function importRedisSnapshot(Redis $redis, $prefix, array $payload)
    {
        $totals = isset($payload['totals']) && is_array($payload['totals']) ? $payload['totals'] : array();
        $days = isset($payload['days']) && is_array($payload['days']) ? $payload['days'] : array();
        $pathsHtml = isset($payload['paths_html']) && is_array($payload['paths_html']) ? $payload['paths_html'] : array();
        $pathsJson = isset($payload['paths_json']) && is_array($payload['paths_json']) ? $payload['paths_json'] : array();
        $pages = isset($payload['pages']) && is_array($payload['pages']) ? $payload['pages'] : array();

        try {
            $redis->multi(Redis::PIPELINE);
            if (isset($totals['all'])) {
                $redis->set($prefix . ':total', (int) $totals['all']);
            }
            if (isset($totals['html'])) {
                $redis->set($prefix . ':html', (int) $totals['html']);
            }
            if (isset($totals['json'])) {
                $redis->set($prefix . ':json', (int) $totals['json']);
            }
            foreach ($days as $field => $value) {
                $redis->hSet($prefix . ':days', $field, (int) $value);
            }
            foreach ($pathsHtml as $path => $score) {
                $redis->zAdd($prefix . ':paths:html', (int) $score, $path);
            }
            foreach ($pathsJson as $path => $score) {
                $redis->zAdd($prefix . ':paths:json', (int) $score, $path);
            }
            foreach ($pages as $pageId => $score) {
                $redis->hSet($prefix . ':pages', $pageId, (int) $score);
            }
            $redis->exec();
        } catch (Exception $e) {
            // keep Redis empty; next request may retry
        }
    }

    private function attachReadOnlyContext($pico, array &$twigVariables, $currentPage)
    {
        if ($currentPage === null || !isset($currentPage['id'])) {
            return;
        }

        $id = $currentPage['id'];
        $template = isset($currentPage['meta']['template'])
            ? strtolower((string) $currentPage['meta']['template'])
            : '';

        if ($template === 'stats' || $id === 'estadisticas' || $id === 'en/stats') {
            $twigVariables['visit_stats_report'] = $this->buildReport($pico);
        }

        if (strpos($id, 'blog/') === 0) {
            $views = $this->getPageViews($id);
            if ($views !== null) {
                $twigVariables['visit_stats_post_views'] = $views;
            }
        }
    }

    private function getPageViews($pageId)
    {
        $redis = $this->getRedis();
        if ($redis === null) {
            return null;
        }

        $prefix = rtrim((string) $this->config['redis_prefix'], ':');
        try {
            $value = $redis->hGet($prefix . ':pages', $pageId);
            return $value === false ? 0 : (int) $value;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildReport($pico)
    {
        $redis = $this->getRedis();
        $empty = array(
            'available' => false,
            'totals' => array('all' => 0, 'html' => 0, 'json' => 0),
            'daily' => array(),
            'top_html' => array(),
            'top_json' => array(),
            'top_posts' => array(),
        );

        if ($redis === null) {
            return $empty;
        }

        $prefix = rtrim((string) $this->config['redis_prefix'], ':');

        try {
            $totals = array(
                'all' => (int) $redis->get($prefix . ':total'),
                'html' => (int) $redis->get($prefix . ':html'),
                'json' => (int) $redis->get($prefix . ':json'),
            );

            $daily = $this->readDailySeries($redis, $prefix, 30);
            $topHtml = $this->readTopPaths($redis, $prefix, 'html', 15);
            $topJson = $this->readTopPaths($redis, $prefix, 'json', 15);
            $topPosts = $this->readTopPosts($redis, $prefix, $pico, 15);

            return array(
                'available' => true,
                'totals' => $totals,
                'daily' => $daily,
                'top_html' => $topHtml,
                'top_json' => $topJson,
                'top_posts' => $topPosts,
            );
        } catch (Exception $e) {
            return $empty;
        }
    }

    private function readDailySeries(Redis $redis, $prefix, $days)
    {
        $raw = $redis->hGetAll($prefix . ':days');
        if (!is_array($raw)) {
            $raw = array();
        }

        $byDay = array();
        foreach ($raw as $field => $value) {
            if (!preg_match('/^(\d{8}):(total|html|json)$/', $field, $matches)) {
                continue;
            }
            $day = $matches[1];
            $metric = $matches[2];
            if (!isset($byDay[$day])) {
                $byDay[$day] = array('total' => 0, 'html' => 0, 'json' => 0);
            }
            $byDay[$day][$metric] = (int) $value;
        }

        krsort($byDay);
        $slice = array_slice($byDay, 0, $days, true);
        $out = array();
        foreach ($slice as $day => $metrics) {
            $out[] = array(
                'day' => $day,
                'label' => substr($day, 0, 4) . '-' . substr($day, 4, 2) . '-' . substr($day, 6, 2),
                'total' => $metrics['total'],
                'html' => $metrics['html'],
                'json' => $metrics['json'],
            );
        }

        return array_reverse($out);
    }

    private function readTopPaths(Redis $redis, $prefix, $kind, $limit)
    {
        $rows = $redis->zRevRange($prefix . ':paths:' . $kind, 0, $limit - 1, true);
        if (!is_array($rows)) {
            return array();
        }

        $out = array();
        foreach ($rows as $path => $score) {
            $out[] = array(
                'path' => $path,
                'views' => (int) $score,
            );
        }

        return $out;
    }

    private function readTopPosts(Redis $redis, $prefix, $pico, $limit)
    {
        $rows = $redis->hGetAll($prefix . ':pages');
        if (!is_array($rows) || $rows === array()) {
            return array();
        }

        arsort($rows, SORT_NUMERIC);
        $rows = array_slice($rows, 0, $limit, true);

        $pagesById = array();
        foreach ($pico->getPages() as $page) {
            if (isset($page['id'])) {
                $pagesById[$page['id']] = $page;
            }
        }

        $out = array();
        foreach ($rows as $pageId => $score) {
            $page = isset($pagesById[$pageId]) ? $pagesById[$pageId] : null;
            $title = ($page !== null && isset($page['title'])) ? $page['title'] : $pageId;
            $url = ($page !== null && isset($page['url'])) ? $page['url'] : null;
            $out[] = array(
                'page_id' => $pageId,
                'title' => $title,
                'url' => $url,
                'views' => (int) $score,
            );
        }

        return $out;
    }

    private function emitPublicStatsJson($pico)
    {
        $report = $this->buildReport($pico);

        header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: public, max-age=300');

        $payload = array(
            'meta' => array(
                'generated_at' => gmdate('c'),
                'schema' => self::SCHEMA_VERSION,
                'source' => 'redis',
                'available' => $report['available'],
            ),
            'totals' => $report['totals'],
            'daily_last_30' => $report['daily'],
            'top_html' => $report['top_html'],
            'top_json' => $report['top_json'],
            'top_posts' => $report['top_posts'],
        );

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * @return Redis|null
     */
    private function getRedis()
    {
        if ($this->redisTried) {
            return $this->redis;
        }
        $this->redisTried = true;

        if (!class_exists('Redis')) {
            return null;
        }

        $host = isset($this->config['redis_host']) ? (string) $this->config['redis_host'] : '127.0.0.1';
        $port = isset($this->config['redis_port']) ? (int) $this->config['redis_port'] : 6379;
        $timeout = isset($this->config['redis_timeout']) ? (float) $this->config['redis_timeout'] : 0.5;

        try {
            $redis = new Redis();
            if (!$redis->connect($host, $port, $timeout)) {
                return null;
            }
            $password = isset($this->config['redis_password']) ? $this->config['redis_password'] : null;
            if (is_string($password) && $password !== '') {
                if (!$redis->auth($password)) {
                    return null;
                }
            }
            $this->redis = $redis;
            $prefix = rtrim((string) $this->config['redis_prefix'], ':');
            $this->tryRestoreFromDisk($redis, $prefix);
        } catch (Exception $e) {
            $this->redis = null;
        }

        return $this->redis;
    }
}
