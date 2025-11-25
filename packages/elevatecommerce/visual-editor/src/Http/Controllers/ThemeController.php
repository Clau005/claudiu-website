<?php

namespace ElevateCommerce\VisualEditor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ElevateCommerce\VisualEditor\Models\Theme;

class ThemeController extends Controller
{
    /**
     * Display a listing of themes.
     */
    public function index()
    {
        $themes = Theme::orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $themeLoader = app('visual-editor.theme-loader');
        $availableThemes = $themeLoader->getAvailableThemes();

        return view('visual-editor::admin.themes.index', [
            'themes' => $themes,
            'availableThemes' => $availableThemes,
            'navigation' => app('visual-editor.navigation')->all(),
        ]);
    }

    /**
     * Activate a theme.
     */
    public function activate(string $slug)
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();
        $theme->activate();

        return redirect()
            ->route('admin.themes.index')
            ->with('success', "Theme '{$theme->name}' activated successfully!");
    }

    /**
     * Duplicate a theme.
     */
    public function duplicate(Request $request, string $slug)
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();
        $newName = $request->input('name', $theme->name . ' Copy');
        
        $newTheme = $theme->duplicate($newName);

        return redirect()
            ->route('admin.themes.index')
            ->with('success', "Theme duplicated as '{$newTheme->name}'!");
    }

    /**
     * Delete a theme.
     */
    public function destroy(string $slug)
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();

        if ($theme->is_active) {
            return redirect()
                ->route('admin.themes.index')
                ->with('error', 'Cannot delete the active theme!');
        }

        $theme->delete();

        return redirect()
            ->route('admin.themes.index')
            ->with('success', "Theme '{$theme->name}' deleted successfully!");
    }
}
