<?php

/**
 * - On blog posts: previous/next by publication time and same-tag "related" posts.
 * - On the categories index: per-tag post counts for the template.
 */
class BlogNeighbors extends AbstractPicoPlugin
{
    public function onPageRendering(&$twig, &$twigVariables, &$templateName)
    {
        $current = $this->getPico()->getCurrentPage();
        if ($current === null || !isset($current['id'])) {
            return;
        }

        $allPages = $this->getPico()->getPages();
        $id = $current['id'];

        if ($id === 'categorias' || $id === 'en/categorias') {
            $tagLang = ($id === 'en/categorias') ? 'en' : 'es';
            $tagCounts = array();
            foreach ($allPages as $p) {
                if (!isset($p['id']) || strpos($p['id'], 'blog/') !== 0) {
                    continue;
                }
                if (class_exists('Multilingual', false) && Multilingual::inferLang($p) !== $tagLang) {
                    continue;
                }
                $tgs = $this->parseTagList(isset($p['meta']['tags']) ? $p['meta']['tags'] : null);
                foreach ($tgs as $t) {
                    if ($t === '') {
                        continue;
                    }
                    if (!isset($tagCounts[$t])) {
                        $tagCounts[$t] = 0;
                    }
                    $tagCounts[$t]++;
                }
            }
            $twigVariables['tag_post_counts'] = $tagCounts;
            return;
        }

        if (strpos($id, 'blog/') !== 0) {
            return;
        }

        $curLang = class_exists('Multilingual', false) ? Multilingual::inferLang($current) : 'es';
        $blogPages = array();
        foreach ($allPages as $p) {
            if (isset($p['id'], $p['time']) && strpos($p['id'], 'blog/') === 0) {
                if (class_exists('Multilingual', false) && Multilingual::inferLang($p) !== $curLang) {
                    continue;
                }
                $blogPages[] = $p;
            }
        }
        if (count($blogPages) < 1) {
            return;
        }

        usort($blogPages, function ($a, $b) {
            if ($a['time'] == $b['time']) {
                return 0;
            }
            return $a['time'] < $b['time'] ? -1 : 1;
        });

        $ids = array_column($blogPages, 'id');
        $pos = array_search($id, $ids, true);
        if ($pos === false) {
            return;
        }

        $twigVariables['post_prev_in_time'] = $pos > 0 ? $blogPages[$pos - 1] : null;
        $twigVariables['post_next_in_time'] = $pos < count($blogPages) - 1 ? $blogPages[$pos + 1] : null;

        $curTags = $this->parseTagList(isset($current['meta']['tags']) ? $current['meta']['tags'] : null);
        $related = array();
        if (!empty($curTags)) {
            foreach ($blogPages as $cand) {
                if ($cand['id'] === $id) {
                    continue;
                }
                $ot = $this->parseTagList(isset($cand['meta']['tags']) ? $cand['meta']['tags'] : null);
                if (count(array_intersect($curTags, $ot)) > 0) {
                    $related[] = $cand;
                }
            }
        }
        usort($related, function ($a, $b) {
            if ($a['time'] == $b['time']) {
                return 0;
            }
            return $a['time'] > $b['time'] ? -1 : 1;
        });
        $twigVariables['related_posts'] = array_slice($related, 0, 5);
    }

    private function parseTagList($tags)
    {
        if (is_array($tags)) {
            return array_map('trim', $tags);
        }
        if (!is_string($tags) || $tags === '') {
            return array();
        }
        $parts = explode(',', $tags);
        return is_array($parts) ? array_map('trim', $parts) : array();
    }
}
