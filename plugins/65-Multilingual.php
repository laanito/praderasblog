<?php

/**
 * Phase 5 — ES + EN: Lang metadata, translation pairs, hreflang, home URL, and Twig UI context.
 *
 * Front matter (optional):
 * - Lang: es | en
 * - Translation_Key: shared string pairing translations (same key on ES and EN files)
 *
 * URL layout (Option A subset):
 * - Spanish posts: content/blog/*.md → /blog/slug (default)
 * - English posts: content/blog/en/*.md → /blog/en/slug
 * - English top pages: content/en/*.md → /en/slug (e.g. /en/series, /en/categorias)
 */
class Multilingual extends AbstractPicoPlugin
{
    private $alternatesByKey = array();

    public function onMetaHeaders(array &$headers)
    {
        $headers['lang'] = 'Lang';
        $headers['translation_key'] = 'Translation_Key';
    }

    public function onPagesLoaded(array &$pages, &$currentPage, &$previousPage, &$nextPage)
    {
        $this->alternatesByKey = array();
        foreach ($pages as $page) {
            if (!isset($page['id'], $page['meta'])) {
                continue;
            }
            $key = $this->readTranslationKey($page['meta']);
            if ($key === '') {
                continue;
            }
            $lang = self::inferLang($page);
            if (!isset($this->alternatesByKey[$key])) {
                $this->alternatesByKey[$key] = array();
            }
            $this->alternatesByKey[$key][$lang] = $page;
        }
    }

    public function onPageRendering(&$twig, &$twigVariables, &$templateName)
    {
        $pico = $this->getPico();
        $current = $pico->getCurrentPage();
        $pages = isset($twigVariables['pages']) ? $twigVariables['pages'] : $pico->getPages();

        $lang = 'es';
        $alternate = null;
        if ($current !== null) {
            $lang = self::inferLang($current);
            $key = $this->readTranslationKey(isset($current['meta']) ? $current['meta'] : array());
            if ($key !== '' && isset($this->alternatesByKey[$key])) {
                $pair = $this->alternatesByKey[$key];
                if ($lang === 'es' && isset($pair['en'])) {
                    $alternate = $pair['en'];
                } elseif ($lang === 'en' && isset($pair['es'])) {
                    $alternate = $pair['es'];
                }
            }
        }

        $twigVariables['content_lang'] = $lang;
        $twigVariables['html_lang'] = $lang === 'en' ? 'en' : 'es';
        $twigVariables['og_locale'] = $lang === 'en' ? 'en_US' : 'es_ES';
        $twigVariables['og_locale_alternate'] = null;
        if ($alternate !== null) {
            $altLang = self::inferLang($alternate);
            $twigVariables['og_locale_alternate'] = $altLang === 'en' ? 'en_US' : 'es_ES';
        }
        $twigVariables['alternate_language_page'] = $alternate;
        $twigVariables['pradera_home_url'] = $this->resolvePageUrl($lang === 'en' ? 'en/index' : 'index', $pages);

        $hreflang = array();
        if ($current !== null) {
            $key = $this->readTranslationKey(isset($current['meta']) ? $current['meta'] : array());
            if ($key !== '' && isset($this->alternatesByKey[$key])) {
                foreach ($this->alternatesByKey[$key] as $lng => $pg) {
                    if (isset($pg['url'])) {
                        $hreflang[] = array(
                            'hreflang' => $lng,
                            'href' => $pg['url'],
                        );
                    }
                }
                if (count($hreflang) > 1) {
                    $esUrl = isset($this->alternatesByKey[$key]['es']['url']) ? $this->alternatesByKey[$key]['es']['url'] : null;
                    $enUrl = isset($this->alternatesByKey[$key]['en']['url']) ? $this->alternatesByKey[$key]['en']['url'] : null;
                    if ($esUrl !== null) {
                        $hreflang[] = array('hreflang' => 'x-default', 'href' => $esUrl);
                    } elseif ($enUrl !== null) {
                        $hreflang[] = array('hreflang' => 'x-default', 'href' => $enUrl);
                    }
                }
            }
        }
        $twigVariables['hreflang_alternates'] = $hreflang;
    }

    public static function inferLang(array $page)
    {
        $meta = isset($page['meta']) ? $page['meta'] : array();
        $raw = '';
        if (isset($meta['lang'])) {
            $raw = $meta['lang'];
        } elseif (isset($meta['Lang'])) {
            $raw = $meta['Lang'];
        }
        if (is_string($raw)) {
            $l = strtolower(trim($raw));
            if ($l === 'en') {
                return 'en';
            }
            if ($l === 'es') {
                return 'es';
            }
        }
        $id = isset($page['id']) ? $page['id'] : '';
        if (strpos($id, 'blog/en/') === 0) {
            return 'en';
        }
        if (strpos($id, 'en/') === 0) {
            return 'en';
        }
        return 'es';
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

    private function resolvePageUrl($id, $pages)
    {
        foreach ($pages as $page) {
            if (isset($page['id']) && $page['id'] === $id && isset($page['url'])) {
                return $page['url'];
            }
        }
        return $this->getPico()->getBaseUrl();
    }
}
