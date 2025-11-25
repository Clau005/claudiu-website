<?php

namespace ElevateCommerce\VisualEditor\Http\Controllers;

use Illuminate\Routing\Controller;
use ElevateCommerce\VisualEditor\Models\Collection;
use ElevateCommerce\VisualEditor\Models\Page;
use ElevateCommerce\VisualEditor\Services\PageRenderService;

class CollectionPublicController extends Controller
{
    public function __construct(
        protected PageRenderService $renderService
    ) {}

    /**
     * Display a single published collection.
     */
    public function show(string $slug)
    {
        $collection = Collection::where('slug', $slug)
            ->where('is_published', true)
            ->with('collectables.collectable')
            ->firstOrFail();

        // Use collection's custom page or default collection template
        $page = $collection->page 
            ?? Page::where('context_key', 'collection')->first();

        if (!$page) {
            abort(404, 'Collection page template not found');
        }

        // Delegate rendering to service (with cached header/footer)
        $viewData = $this->renderService->renderPage($page, $collection, cache: true);

        return view('visual-editor::pages.render', $viewData);
    }
}
