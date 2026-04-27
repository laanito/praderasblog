<?php

/**
 * Series / collections support for blog posts.
 *
 * Front matter (optional on posts):
 * - Series
 * - Series_Slug
 * - Series_Order
 *
 * Features:
 * - /series/<slug>/ pages backed by content/series.md
 * - In-post previous/next/index navigation within the same series
 */
class SeriesCollections extends AbstractPicoPlugin
{
    private $requestedSeriesSlug = null;

    public function onMetaHeaders(&$headers)
    {
        $headers['series'] = 'Series';
        $headers['series_slug'] = 'Series_Slug';
        $headers['series_order'] = 'Series_Order';
    }

    public function onRequestUrl(&$url)
    {
        if (preg_match('~^series/([^/]+)/?$~', $url, $matches)) {
            $this->requestedSeriesSlug = $this->slugify(rawurldecode($matches[1]));
        }
    }

    public function onRequestFile(&$file)
    {
        if ($this->requestedSeriesSlug === null) {
            return;
        }

        $pico = $this->getPico();
        $seriesFile = $pico->getConfig('content_dir') . 'series' . $pico->getConfig('content_ext');
        if (file_exists($seriesFile)) {
            $file = $seriesFile;
        }
    }

    public function onPageRendering(&$twig, &$twigVariables, &$templateName)
    {
        $current = $this->getPico()->getCurrentPage();
        if ($current === null || !isset($current['id'])) {
            return;
        }

        $seriesMap = $this->buildSeriesMap($this->getPico()->getPages());
        $twigVariables['series_collections'] = array_values($seriesMap);

        if ($current['id'] === 'series' && $this->requestedSeriesSlug !== null) {
            $twigVariables['series_slug'] = $this->requestedSeriesSlug;
            $twigVariables['current_series'] = isset($seriesMap[$this->requestedSeriesSlug])
                ? $seriesMap[$this->requestedSeriesSlug]
                : null;
        }

        if (strpos($current['id'], 'blog/') !== 0) {
            return;
        }

        $currentSlug = $this->extractSeriesSlug(isset($current['meta']) ? $current['meta'] : array());
        if ($currentSlug === '' || !isset($seriesMap[$currentSlug])) {
            return;
        }

        $series = $seriesMap[$currentSlug];
        $ids = array_column($series['entries'], 'id');
        $pos = array_search($current['id'], $ids, true);
        if ($pos === false) {
            return;
        }

        $twigVariables['post_series'] = array(
            'name' => $series['name'],
            'slug' => $series['slug'],
            'url' => $series['url'],
            'total' => count($series['entries']),
            'index' => $pos + 1,
            'prev' => $pos > 0 ? $series['entries'][$pos - 1] : null,
            'next' => $pos < count($series['entries']) - 1 ? $series['entries'][$pos + 1] : null,
        );
    }

    private function buildSeriesMap($pages)
    {
        $seriesMap = array();

        foreach ($pages as $page) {
            if (!isset($page['id'], $page['time']) || strpos($page['id'], 'blog/') !== 0) {
                continue;
            }

            $meta = isset($page['meta']) ? $page['meta'] : array();
            $slug = $this->extractSeriesSlug($meta);
            if ($slug === '') {
                continue;
            }

            $name = $this->extractSeriesName($meta, $slug);
            if (!isset($seriesMap[$slug])) {
                $seriesMap[$slug] = array(
                    'name' => $name,
                    'slug' => $slug,
                    'url' => $this->buildSeriesUrl($slug),
                    'entries' => array(),
                );
            } elseif ($seriesMap[$slug]['name'] === $this->humanizeSlug($slug) && $name !== '') {
                $seriesMap[$slug]['name'] = $name;
            }

            $seriesMap[$slug]['entries'][] = array(
                'id' => $page['id'],
                'title' => isset($page['title']) ? $page['title'] : $page['id'],
                'url' => isset($page['url']) ? $page['url'] : '',
                'date' => isset($page['date']) ? $page['date'] : '',
                'time' => $page['time'],
                'series_order' => $this->extractSeriesOrder($meta),
            );
        }

        foreach ($seriesMap as &$series) {
            usort($series['entries'], function ($a, $b) {
                if ($a['series_order'] !== null && $b['series_order'] !== null && $a['series_order'] !== $b['series_order']) {
                    return $a['series_order'] < $b['series_order'] ? -1 : 1;
                }

                if ($a['series_order'] !== null && $b['series_order'] === null) {
                    return -1;
                }

                if ($a['series_order'] === null && $b['series_order'] !== null) {
                    return 1;
                }

                if ($a['time'] !== $b['time']) {
                    return $a['time'] < $b['time'] ? -1 : 1;
                }

                return strcmp($a['id'], $b['id']);
            });
        }
        unset($series);

        uasort($seriesMap, function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        return $seriesMap;
    }

    private function extractSeriesSlug($meta)
    {
        $slug = isset($meta['series_slug']) ? trim((string)$meta['series_slug']) : '';
        if ($slug !== '') {
            return $this->slugify($slug);
        }

        $name = isset($meta['series']) ? trim((string)$meta['series']) : '';
        if ($name !== '') {
            return $this->slugify($name);
        }

        return '';
    }

    private function extractSeriesName($meta, $slug)
    {
        $name = isset($meta['series']) ? trim((string)$meta['series']) : '';
        return $name !== '' ? $name : $this->humanizeSlug($slug);
    }

    private function extractSeriesOrder($meta)
    {
        if (!isset($meta['series_order'])) {
            return null;
        }

        $value = trim((string)$meta['series_order']);
        if ($value === '' || !is_numeric($value)) {
            return null;
        }

        return (int)$value;
    }

    private function buildSeriesUrl($slug)
    {
        return $this->getPico()->getBaseUrl() . 'series/' . rawurlencode($slug) . '/';
    }

    private function humanizeSlug($slug)
    {
        $parts = explode('-', $slug);
        $parts = array_map(function ($part) {
            return ucfirst($part);
        }, $parts);

        return implode(' ', $parts);
    }

    private function slugify($value)
    {
        $value = trim(mb_strtolower((string)$value));
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if ($ascii === false) {
            $ascii = $value;
        }

        $slug = preg_replace('~[^a-z0-9]+~', '-', $ascii);
        $slug = trim((string)$slug, '-');
        return $slug;
    }
}
