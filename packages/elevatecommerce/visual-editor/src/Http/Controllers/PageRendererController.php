<?php

namespace ElevateCommerce\VisualEditor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ElevateCommerce\VisualEditor\Models\Theme;
use ElevateCommerce\VisualEditor\Services\PageRenderService;

class PageRendererController extends Controller
{
    public function __construct(
        protected PageRenderService $renderService
    ) {}

    /**
     * Render any page by slug (catch-all route).
     *
     * @param Request $request
     * @param string|null $slug
     * @return \Illuminate\View\View
     */
    public function render(Request $request, ?string $slug = null)
    {
        // Default to home page if no slug provided
        $pageSlug = $slug ?: 'home';
        
        // Cache page lookup for 1 hour
        $cacheKey = "page:{$pageSlug}";
        
        $page = cache()->remember($cacheKey, 3600, function () use ($pageSlug) {
            $theme = Theme::active();

            if (!$theme) {
                return null;
            }

            return $theme->pages()
                ->where('slug', $pageSlug)
                ->where('type', 'static')
                ->where('is_published', true)
                ->first();
        });

        if (!$page) {
            abort(404, 'Page not found');
        }

        // Fetch context data if page has a context key
        $contextData = null;
        if ($page->context_key) {
            $contextRegistry = app('visual-editor.context');
            $contextData = $contextRegistry->fetch($page->context_key, $request, []);

            if (!$contextData) {
                abort(404, 'Context data not found');
            }
        }

        // Render page with cached header/footer (service handles caching)
        $viewData = $this->renderService->renderPage($page, $contextData, cache: true);

        return view('visual-editor::pages.render', $viewData);
    }
}
