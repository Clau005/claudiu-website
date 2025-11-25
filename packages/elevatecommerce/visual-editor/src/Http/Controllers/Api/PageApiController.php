<?php

namespace ElevateCommerce\VisualEditor\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ElevateCommerce\VisualEditor\Models\Page;

class PageApiController extends Controller
{
    /**
     * Get page data for editor.
     */
    public function show(int $id)
    {
        $page = Page::with('theme')->findOrFail($id);
        
        // Load theme sections
        $themeLoader = app('visual-editor.theme-loader');
        $themeLoader->loadTheme($page->theme->slug);
        
        // Get available sections for this theme
        $sectionRegistry = app('visual-editor.section');
        $allSections = $sectionRegistry->all();
        
        // Filter sections for this theme
        $availableSections = collect($allSections)
            ->filter(function ($section, $key) use ($page) {
                return str_starts_with($key, $page->theme->slug . '-');
            })
            ->map(function ($section, $key) {
                return [
                    'key' => $key,
                    'label' => $section['label'],
                    'category' => $section['category'],
                    'icon' => $section['icon'],
                    'schema' => $section['schema'],
                    'defaults' => $section['defaults'],
                ];
            })
            ->values();

        return response()->json([
            'page' => $page,
            'theme' => $page->theme,
            'availableSections' => $availableSections,
        ]);
    }

    /**
     * Update page draft configuration.
     */
    public function update(Request $request, int $id)
    {
        $page = Page::findOrFail($id);
        
        $validated = $request->validate([
            'draft_config' => 'nullable|array',
        ]);

        $page->updateDraft($validated['draft_config'] ?? []);

        return response()->json([
            'success' => true,
            'page' => $page->fresh(),
        ]);
    }

    /**
     * Get all pages for a theme.
     */
    public function themePages(int $themeId)
    {
        $pages = Page::where('theme_id', $themeId)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'type']);

        return response()->json([
            'pages' => $pages,
        ]);
    }
}
