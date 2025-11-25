<?php

namespace ElevateCommerce\VisualEditor\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ElevateCommerce\VisualEditor\Models\Theme;
use Illuminate\Support\Facades\Cache;

class ThemeApiController extends Controller
{
    /**
     * Update theme header/footer configuration.
     */
    public function update(Request $request, int $id)
    {
        $theme = Theme::findOrFail($id);
        
        $validated = $request->validate([
            'header_config' => 'nullable|array',
            'footer_config' => 'nullable|array',
            'header_config_draft' => 'nullable|array',
            'footer_config_draft' => 'nullable|array',
        ]);

        $theme->update($validated);

        // Clear header/footer cache when config changes
        $this->clearThemeCache($theme);

        return response()->json([
            'success' => true,
            'theme' => $theme->fresh(),
        ]);
    }

    /**
     * Publish theme header/footer draft configuration.
     */
    public function publish(Request $request, int $id)
    {
        $theme = Theme::findOrFail($id);
        
        $theme->publishHeaderFooter();
        
        return response()->json([
            'success' => true,
            'theme' => $theme->fresh(),
        ]);
    }

    /**
     * Clear theme header/footer cache.
     */
    protected function clearThemeCache(Theme $theme)
    {
        // Cache keys include config hash, so they auto-invalidate when config changes
        // No need to manually clear - the hash changes = new cache key
        // This keeps it compatible with all cache drivers (file, redis, memcached)
    }
}
