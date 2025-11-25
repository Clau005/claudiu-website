<?php

namespace ElevateCommerce\VisualEditor\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ElevateCommerce\VisualEditor\Models\Collection;

class CollectionApiController extends Controller
{
    /**
     * Get available items that can be added to a collection.
     */
    public function availableItems(Request $request, int $id)
    {
        $collection = Collection::findOrFail($id);
        
        $contextType = $request->get('context_type');
        
        // Get context registry to fetch available models
        $contextRegistry = app('visual-editor.context');
        
        if (!$contextType || !$contextRegistry->isRegistered($contextType)) {
            return response()->json([
                'items' => [],
                'message' => 'Invalid or missing context type'
            ]);
        }
        
        // Fetch items from the context
        $items = $contextRegistry->fetch($contextType, $request, []);
        
        return response()->json([
            'items' => $items,
            'context_type' => $contextType,
        ]);
    }

    /**
     * Get items in a collection.
     */
    public function items(int $id)
    {
        $collection = Collection::with('collectables.collectable')->findOrFail($id);
        
        return response()->json([
            'items' => $collection->collectables->map(function ($collectable) {
                return [
                    'id' => $collectable->id,
                    'position' => $collectable->position,
                    'type' => $collectable->collectable_type,
                    'item' => $collectable->collectable,
                ];
            }),
        ]);
    }
}
