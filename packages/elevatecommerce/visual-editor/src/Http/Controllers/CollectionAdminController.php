<?php

namespace ElevateCommerce\VisualEditor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use ElevateCommerce\VisualEditor\Models\Collection;
use ElevateCommerce\VisualEditor\Models\Collectable;
use ElevateCommerce\VisualEditor\Models\Page;

class CollectionAdminController extends Controller
{
    /**
     * Display a listing of collections.
     */
    public function index(Request $request)
    {
        $query = Collection::with('page');

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortableColumns = ['title', 'type', 'is_published', 'created_at', 'updated_at'];
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortColumn, $sortableColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $collections = $query->paginate(20)->withQueryString();

        return view('visual-editor::admin.collections.index', [
            'collections' => $collections,
            'navigation' => app('visual-editor.navigation')->all(),
        ]);
    }

    /**
     * Show the form for creating a new collection.
     */
    public function create()
    {
        $pages = Page::where('context_key', 'collection')
            ->orderBy('name')
            ->get();

        return view('visual-editor::admin.collections.create', [
            'pages' => $pages,
            'navigation' => app('visual-editor.navigation')->all(),
        ]);
    }

    /**
     * Store a newly created collection.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:collections,slug',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'image' => 'nullable|string',
            'type' => 'required|in:manual,smart',
            'conditions' => 'nullable|array',
            'page_id' => 'nullable|exists:pages,id',
            'metafields' => 'nullable|array',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $collection = Collection::create($validated);

        return redirect()
            ->route('admin.collections.edit', $collection)
            ->with('success', "Collection '{$collection->title}' created successfully!");
    }

    /**
     * Show the form for editing a collection.
     */
    public function edit(int $id)
    {
        $collection = Collection::with(['page', 'collectables.collectable'])->findOrFail($id);
        
        $pages = Page::where('context_key', 'collection')
            ->orderBy('name')
            ->get();

        // Get collectable types from context registry
        $contextRegistry = app('visual-editor.context');
        $collectableTypes = collect($contextRegistry->all())
            ->filter(fn($context) => isset($context['collectable']) && $context['collectable'])
            ->map(fn($context, $key) => [
                'key' => $key,
                'label' => ucfirst($key),
                'model' => $context['model'] ?? null,
            ])
            ->values()
            ->toArray();

        return view('visual-editor::admin.collections.edit', [
            'collection' => $collection,
            'pages' => $pages,
            'collectableTypes' => $collectableTypes,
            'navigation' => app('visual-editor.navigation')->all(),
        ]);
    }

    /**
     * Update the specified collection.
     */
    public function update(Request $request, int $id)
    {
        $collection = Collection::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:collections,slug,' . $id,
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'image' => 'nullable|string',
            'type' => 'required|in:manual,smart',
            'conditions' => 'nullable|array',
            'page_id' => 'nullable|exists:pages,id',
            'metafields' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        $collection->update($validated);

        // Always sync tags (even if empty array to remove all tags)
        $collection->syncTags($request->input('tags', []));

        return redirect()
            ->route('admin.collections.edit', $collection)
            ->with('success', "Collection '{$collection->title}' updated successfully!");
    }

    /**
     * Remove the specified collection.
     */
    public function destroy(int $id)
    {
        $collection = Collection::findOrFail($id);
        $title = $collection->title;
        $collection->delete();

        return redirect()
            ->route('admin.collections.index')
            ->with('success', "Collection '{$title}' deleted successfully!");
    }

    /**
     * Publish a collection.
     */
    public function publish(int $id)
    {
        $collection = Collection::findOrFail($id);
        $collection->publish();

        return redirect()
            ->route('admin.collections.edit', $collection)
            ->with('success', "Collection '{$collection->title}' published successfully!");
    }

    /**
     * Unpublish a collection.
     */
    public function unpublish(int $id)
    {
        $collection = Collection::findOrFail($id);
        $collection->unpublish();

        return redirect()
            ->route('admin.collections.edit', $collection)
            ->with('success', "Collection '{$collection->title}' unpublished successfully!");
    }

    /**
     * Add an item to a collection.
     */
    public function addItem(Request $request, int $id)
    {
        $collection = Collection::findOrFail($id);

        $validated = $request->validate([
            'collectable_type' => 'required|string',
            'collectable_id' => 'required|integer',
            'position' => 'nullable|integer',
        ]);

        $collectable = $collection->collectables()->create($validated);

        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Item added to collection!',
                'collectable' => $collectable
            ]);
        }

        return back()->with('success', 'Item added to collection!');
    }

    /**
     * Remove an item from a collection.
     */
    public function removeItem(Request $request, int $collectionId, int $collectableId)
    {
        $collectable = Collectable::where('collection_id', $collectionId)
            ->where('id', $collectableId)
            ->firstOrFail();

        $collectable->delete();

        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed from collection!'
            ]);
        }

        return back()->with('success', 'Item removed from collection!');
    }

    /**
     * Reorder collection items.
     */
    public function reorderItems(Request $request, int $id)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:collectables,id',
            'items.*.position' => 'required|integer',
        ]);

        foreach ($validated['items'] as $item) {
            Collectable::where('id', $item['id'])->update(['position' => $item['position']]);
        }

        return response()->json(['success' => true]);
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

        $collections = Collection::whereIn('id', $ids)->get();

        switch ($action) {
            case 'delete':
                foreach ($collections as $collection) {
                    $collection->delete();
                }
                $count = count($collections);
                $message = $count === 1 ? 'Collection deleted successfully!' : "{$count} collections deleted successfully!";
                break;

            case 'publish':
                foreach ($collections as $collection) {
                    $collection->publish();
                }
                $count = count($collections);
                $message = $count === 1 ? 'Collection published successfully!' : "{$count} collections published successfully!";
                break;

            case 'unpublish':
                foreach ($collections as $collection) {
                    $collection->unpublish();
                }
                $count = count($collections);
                $message = $count === 1 ? 'Collection unpublished successfully!' : "{$count} collections unpublished successfully!";
                break;

            default:
                return redirect()
                    ->back()
                    ->with('error', 'Invalid action');
        }

        return redirect()
            ->route('admin.collections.index')
            ->with('success', $message);
    }
}
