<?php

namespace ElevateCommerce\VisualEditor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ElevateCommerce\VisualEditor\Models\Page;
use ElevateCommerce\VisualEditor\Models\Theme;

class PageController extends Controller
{
    /**
     * Display a listing of pages.
     */
    public function index(Request $request)
    {
        $query = Page::with('theme');

        // Filter by theme
        if ($request->filled('theme')) {
            $themeSlug = $request->get('theme');
            $query->whereHas('theme', function ($q) use ($themeSlug) {
                $q->where('slug', $themeSlug);
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortableColumns = ['name', 'type', 'is_published', 'created_at', 'updated_at'];
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortColumn, $sortableColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $pages = $query->paginate(20)->withQueryString();
        $themes = Theme::all();

        return view('visual-editor::admin.pages.index', [
            'pages' => $pages,
            'themes' => $themes,
            'selectedTheme' => $request->get('theme'),
            'navigation' => app('visual-editor.navigation')->all(),
        ]);
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(Request $request)
    {
        $themes = Theme::all();
        $selectedTheme = $request->get('theme');

        return view('visual-editor::admin.pages.create', [
            'themes' => $themes,
            'selectedTheme' => $selectedTheme,
            'navigation' => app('visual-editor.navigation')->all(),
        ]);
    }

    /**
     * Store a newly created page.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'theme_id' => 'required|exists:themes,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'type' => 'required|in:static,dynamic,template',
            'context_key' => 'nullable|string',
            'route_pattern' => 'nullable|string',
        ]);

        $page = Page::create($validated);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', "Page '{$page->name}' created successfully!");
    }

    /**
     * Show the page editor.
     */
    public function edit(int $id)
    {
        $page = Page::with('theme')->findOrFail($id);

        return view('visual-editor::admin.pages.edit', [
            'page' => $page,
            'navigation' => app('visual-editor.navigation')->all(),
        ]);
    }

    /**
     * Preview page (for iframe in editor).
     */
    public function preview(int $id)
    {
        $page = Page::with('theme')->findOrFail($id);
        
        // Reload theme to get fresh header/footer config
        $theme = \ElevateCommerce\VisualEditor\Models\Theme::find($page->theme_id);
        
        // IMPORTANT: Load theme sections first!
        $themeLoader = app('visual-editor.theme-loader');
        $themeLoader->loadTheme($theme->slug);
        
        // Use draft config for preview
        $config = $page->draft_config ?? [];
        
        // Render sections
        $sectionRegistry = app('visual-editor.section');
        
        // Render header sections (use draft for preview)
        $headerSections = [];
        foreach ($theme->header_config_draft ?? $theme->header_config ?? [] as $sectionConfig) {
            $sectionKey = $sectionConfig['key'] ?? null;
            $settings = $sectionConfig['settings'] ?? [];
            
            if ($sectionKey) {
                $html = $sectionRegistry->render($sectionKey, $settings, null);
                if ($html) {
                    $headerSections[] = [
                        'id' => $sectionConfig['id'] ?? null,
                        'key' => $sectionKey,
                        'html' => $html,
                    ];
                }
            }
        }
        
        // Render template sections
        $templateSections = [];
        foreach ($config as $sectionConfig) {
            $sectionKey = $sectionConfig['key'] ?? null;
            $settings = $sectionConfig['settings'] ?? [];
            
            if ($sectionKey) {
                $html = $sectionRegistry->render($sectionKey, $settings, null);
                if ($html) {
                    $templateSections[] = [
                        'id' => $sectionConfig['id'] ?? null,
                        'key' => $sectionKey,
                        'html' => $html,
                    ];
                }
            }
        }
        
        // Render footer sections (use draft for preview)
        $footerSections = [];
        foreach ($theme->footer_config_draft ?? $theme->footer_config ?? [] as $sectionConfig) {
            $sectionKey = $sectionConfig['key'] ?? null;
            $settings = $sectionConfig['settings'] ?? [];
            
            if ($sectionKey) {
                $html = $sectionRegistry->render($sectionKey, $settings, null);
                if ($html) {
                    $footerSections[] = [
                        'id' => $sectionConfig['id'] ?? null,
                        'key' => $sectionKey,
                        'html' => $html,
                    ];
                }
            }
        }
        
        return response()
            ->view('visual-editor::pages.render', [
                'page' => $page,
                'theme' => $theme,
                'headerSections' => $headerSections,
                'templateSections' => $templateSections,
                'footerSections' => $footerSections,
                'context' => null,
                'preview' => true,
            ])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Publish a page.
     */
    public function publish(int $id)
    {
        $page = Page::findOrFail($id);
        $page->publish();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', "Page '{$page->name}' published successfully!");
    }

    /**
     * Unpublish a page.
     */
    public function unpublish(int $id)
    {
        $page = Page::findOrFail($id);
        $page->unpublish();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', "Page '{$page->name}' unpublished successfully!");
    }

    /**
     * Delete a page.
     */
    public function destroy(int $id)
    {
        $page = Page::findOrFail($id);
        $name = $page->name;
        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', "Page '{$name}' deleted successfully!");
    }

    /**
     * Handle bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,unpublish',
            'ids' => 'required|string',
        ]);

        $ids = array_filter(explode(',', $request->ids));
        $action = $request->action;

        if (empty($ids)) {
            return redirect()
                ->back()
                ->with('error', 'No items selected');
        }

        $pages = Page::whereIn('id', $ids)->get();

        switch ($action) {
            case 'delete':
                foreach ($pages as $page) {
                    $page->delete();
                }
                $count = count($pages);
                $message = $count === 1 ? 'Page deleted successfully!' : "{$count} pages deleted successfully!";
                break;

            case 'publish':
                foreach ($pages as $page) {
                    $page->publish();
                }
                $count = count($pages);
                $message = $count === 1 ? 'Page published successfully!' : "{$count} pages published successfully!";
                break;

            case 'unpublish':
                foreach ($pages as $page) {
                    $page->unpublish();
                }
                $count = count($pages);
                $message = $count === 1 ? 'Page unpublished successfully!' : "{$count} pages unpublished successfully!";
                break;

            default:
                return redirect()
                    ->back()
                    ->with('error', 'Invalid action');
        }

        return redirect()
            ->route('admin.pages.index')
            ->with('success', $message);
    }
}
