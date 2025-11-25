<?php

namespace ElevateCommerce\VisualEditor\Services;

use ElevateCommerce\VisualEditor\Models\Page;
use ElevateCommerce\VisualEditor\Models\Theme;
use ElevateCommerce\VisualEditor\Support\SectionRegistry;

/**
 * Service responsible for rendering pages with their sections.
 * Follows Single Responsibility Principle - only handles page rendering logic.
 */
class PageRenderService
{
    public function __construct(
        protected SectionRegistry $sectionRegistry
    ) {}

    /**
     * Render a complete page with header, template, and footer sections.
     *
     * @param Page $page
     * @param mixed $contextData
     * @param bool $cache Whether to cache header/footer (default: true)
     * @return array View data ready for rendering
     */
    public function renderPage(Page $page, mixed $contextData = null, bool $cache = true): array
    {
        $config = $page->getRenderConfig();

        // Render header and footer (with optional caching)
        $headerSections = $cache 
            ? $this->renderThemeHeaderFooterCached($page->theme, 'header')
            : $this->renderThemeHeaderFooter($page->theme, 'header');
            
        $footerSections = $cache 
            ? $this->renderThemeHeaderFooterCached($page->theme, 'footer')
            : $this->renderThemeHeaderFooter($page->theme, 'footer');
        
        // Render page template sections (if any)
        $templateSections = [];
        if ($config && !empty($config)) {
            $templateSections = $this->renderSections($config, $contextData);
        }

        // Extract SEO data from context if available
        $seo = $this->extractSeoData($contextData);

        return [
            'page' => $page,
            'theme' => $page->theme,
            'headerSections' => $headerSections,
            'templateSections' => $templateSections,
            'footerSections' => $footerSections,
            'context' => $contextData,
            'seo' => $seo,
        ];
    }

    /**
     * Extract SEO data from context model.
     *
     * @param mixed $contextData
     * @return array
     */
    protected function extractSeoData(mixed $contextData): array
    {
        if (!$contextData) {
            return [];
        }

        $seo = [];

        // Check if context has SEO fields
        if (is_object($contextData)) {
            // Meta title
            if (isset($contextData->meta_title) && $contextData->meta_title) {
                $seo['title'] = $contextData->meta_title;
            } elseif (isset($contextData->title) && $contextData->title) {
                $seo['title'] = $contextData->title;
            } elseif (isset($contextData->name) && $contextData->name) {
                $seo['title'] = $contextData->name;
            }

            // Meta description
            if (isset($contextData->meta_description) && $contextData->meta_description) {
                $seo['description'] = $contextData->meta_description;
            } elseif (isset($contextData->excerpt) && $contextData->excerpt) {
                $seo['description'] = $contextData->excerpt;
            } elseif (isset($contextData->description) && $contextData->description) {
                // Strip HTML tags and limit to 160 characters
                $seo['description'] = \Illuminate\Support\Str::limit(strip_tags($contextData->description), 160);
            }

            // Meta keywords
            if (isset($contextData->meta_keywords) && $contextData->meta_keywords) {
                $seo['keywords'] = $contextData->meta_keywords;
            }

            // Image for Open Graph
            if (isset($contextData->image) && $contextData->image) {
                $seo['image'] = $contextData->image;
            } elseif (isset($contextData->preview) && $contextData->preview) {
                $seo['image'] = $contextData->preview;
            } elseif (isset($contextData->featured_image) && $contextData->featured_image) {
                $seo['image'] = $contextData->featured_image;
            }

            // Canonical URL (if custom slug exists)
            if (isset($contextData->slug) && $contextData->slug) {
                $seo['canonical'] = url()->current();
            }

            // Open Graph type (product vs article vs website)
            if (isset($contextData->price)) {
                $seo['og_type'] = 'product';
            }

            // Robots directive (check if item should be indexed)
            if (isset($contextData->is_published) && !$contextData->is_published) {
                $seo['robots'] = 'noindex, nofollow';
            } elseif (isset($contextData->is_active) && !$contextData->is_active) {
                $seo['robots'] = 'noindex, nofollow';
            } elseif (isset($contextData->meta_robots) && $contextData->meta_robots) {
                $seo['robots'] = $contextData->meta_robots;
            }
        }

        return $seo;
    }

    /**
     * Render theme header or footer sections with caching.
     *
     * @param Theme $theme
     * @param string $type 'header' or 'footer'
     * @return array
     */
    public function renderThemeHeaderFooterCached(Theme $theme, string $type): array
    {
        $configKey = $type . '_config';
        $config = $theme->$configKey;

        if (!$config || empty($config)) {
            return [];
        }

        // Cache key includes config hash for instant invalidation
        $configHash = md5(json_encode($config));
        $cacheKey = "theme:{$theme->id}:{$type}:{$configHash}";

        // Cache for 24 hours - header/footer rarely change
        return cache()->remember($cacheKey, 86400, function () use ($config) {
            return $this->renderSections($config, null);
        });
    }

    /**
     * Render theme header or footer sections without caching.
     *
     * @param Theme $theme
     * @param string $type 'header' or 'footer'
     * @return array
     */
    public function renderThemeHeaderFooter(Theme $theme, string $type): array
    {
        $configKey = $type . '_config';
        $config = $theme->$configKey;

        if (!$config || empty($config)) {
            return [];
        }

        return $this->renderSections($config, null);
    }

    /**
     * Render sections with context data.
     *
     * @param array $config Section configuration array
     * @param mixed $contextData Context data to pass to sections
     * @return array Rendered sections
     */
    public function renderSections(array $config, mixed $contextData = null): array
    {
        $rendered = [];

        foreach ($config as $sectionConfig) {
            $sectionKey = $sectionConfig['key'] ?? null;
            $settings = $sectionConfig['settings'] ?? [];
            $id = $sectionConfig['id'] ?? null;

            if (!$sectionKey) {
                continue;
            }

            $html = $this->sectionRegistry->render($sectionKey, $settings, $contextData);

            if ($html) {
                $rendered[] = [
                    'id' => $id,
                    'key' => $sectionKey,
                    'html' => $html,
                ];
            }
        }

        return $rendered;
    }
}
