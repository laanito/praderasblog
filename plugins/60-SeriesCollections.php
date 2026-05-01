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
 * - /series/<slug>/ and /en/series/<slug>/ pages backed by content/series.md
 * - In-post previous/next/index navigation within the same series
 */
class SeriesCollections extends AbstractPicoPlugin
{
    private $requestedSeriesSlug = null;

    /** @var string Language for the current series detail URL (es | en). */
    private $requestedSeriesLang = 'es';

    public function onMetaHeaders(&$headers)
    {
        $headers['series'] = 'Series';
        $headers['series_slug'] = 'Series_Slug';
        $headers['series_order'] = 'Series_Order';
    }

    public function onRequestUrl(&$url)
    {
        $this->requestedSeriesSlug = null;
        $this->requestedSeriesLang = 'es';

        if (preg_match('~^en/series/([^/]+)/?$~', $url, $matches)) {
            $this->requestedSeriesSlug = $this->slugify(rawurldecode($matches[1]));
            $this->requestedSeriesLang = 'en';
        } elseif (preg_match('~^series/([^/]+)/?$~', $url, $matches)) {
            $this->requestedSeriesSlug = $this->slugify(rawurldecode($matches[1]));
            $this->requestedSeriesLang = 'es';
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

        $byLang = $this->buildSeriesMapByLang($this->getPico()->getPages());

        $listLang = ($current['id'] === 'en/series') ? 'en' : 'es';
        $mapForList = isset($byLang[$listLang]) ? $byLang[$listLang] : array();
        uasort($mapForList, function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });
        $twigVariables['series_collections'] = array_values($mapForList);

        $seriesUiLang = $listLang;
        if ($this->requestedSeriesSlug !== null) {
            $seriesUiLang = $this->requestedSeriesLang;
        }
        $twigVariables['series_ui_lang'] = $seriesUiLang;

        if ($this->requestedSeriesSlug !== null) {
            $slug = $this->requestedSeriesSlug;
            $twigVariables['series_slug'] = $slug;
            $twigVariables['current_series'] = null;
            $detailLang = $this->requestedSeriesLang;
            if (isset($byLang[$detailLang][$slug])) {
                $twigVariables['current_series'] = $byLang[$detailLang][$slug];
            }
        }

        if (strpos($current['id'], 'blog/') !== 0) {
            return;
        }

        $curLang = class_exists('Multilingual', false) ? Multilingual::inferLang($current) : 'es';
        if (!isset($byLang[$curLang])) {
            $curLang = 'es';
        }
        $seriesMap = $byLang[$curLang];

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

    private function buildSeriesMapByLang($pages)
    {
        $byLang = array('es' => array(), 'en' => array());

        foreach ($pages as $page) {
            if (!isset($page['id'], $page['time']) || strpos($page['id'], 'blog/') !== 0) {
                continue;
            }

            $lang = class_exists('Multilingual', false) ? Multilingual::inferLang($page) : 'es';
            if (!isset($byLang[$lang])) {
                $lang = 'es';
            }

            $meta = isset($page['meta']) ? $page['meta'] : array();
            $slug = $this->extractSeriesSlug($meta);
            if ($slug === '') {
                continue;
            }

            $name = $this->extractSeriesName($meta, $slug);
            if (!isset($byLang[$lang][$slug])) {
                $byLang[$lang][$slug] = array(
                    'name' => $name,
                    'slug' => $slug,
                    'url' => $this->buildSeriesUrl($slug, $lang),
                    'entries' => array(),
                );
            } elseif ($byLang[$lang][$slug]['name'] === $this->humanizeSlug($slug) && $name !== '') {
                $byLang[$lang][$slug]['name'] = $name;
            }

            $byLang[$lang][$slug]['entries'][] = array(
                'id' => $page['id'],
                'title' => isset($page['title']) ? $page['title'] : $page['id'],
                'url' => isset($page['url']) ? $page['url'] : '',
                'date' => isset($page['date']) ? $page['date'] : '',
                'time' => $page['time'],
                'series_order' => $this->extractSeriesOrder($meta),
            );
        }

        foreach (array('es', 'en') as $lang) {
            if (!isset($byLang[$lang])) {
                continue;
            }
            foreach ($byLang[$lang] as &$series) {
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
        }

        return $byLang;
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

    private function buildSeriesUrl($slug, $lang = 'es')
    {
        $base = $this->getPico()->getBaseUrl();
        if ($lang === 'en') {
            return $base . 'en/series/' . rawurlencode($slug) . '/';
        }

        return $base . 'series/' . rawurlencode($slug) . '/';
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
