<?php

namespace ElevateCommerce\VisualEditor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theme extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'author',
        'is_active',
        'settings',
        'header_config',
        'footer_config',
        'header_config_draft',
        'footer_config_draft',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'header_config' => 'array',
        'footer_config' => 'array',
        'header_config_draft' => 'array',
        'footer_config_draft' => 'array',
    ];

    /**
     * Get the pages for this theme.
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Get the sections for this theme.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(ThemeSection::class);
    }

    /**
     * Activate this theme (deactivate others).
     */
    public function activate(): void
    {
        // Deactivate all other themes
        static::where('id', '!=', $this->id)->update(['is_active' => false]);
        
        // Activate this theme
        $this->update(['is_active' => true]);
    }

    /**
     * Duplicate this theme.
     */
    public function duplicate(string $newName): self
    {
        $newTheme = $this->replicate();
        $newTheme->name = $newName;
        $newTheme->slug = \Illuminate\Support\Str::slug($newName);
        $newTheme->is_active = false;
        $newTheme->save();

        // Duplicate pages
        foreach ($this->pages as $page) {
            $newPage = $page->replicate();
            $newPage->theme_id = $newTheme->id;
            $newPage->is_published = false;
            $newPage->published_config = null;
            $newPage->published_at = null;
            $newPage->save();
        }

        // Duplicate theme sections
        foreach ($this->sections as $section) {
            $newSection = $section->replicate();
            $newSection->theme_id = $newTheme->id;
            $newSection->save();
        }

        return $newTheme;
    }

    /**
     * Publish draft header/footer configuration.
     */
    public function publishHeaderFooter(): void
    {
        $this->update([
            'header_config' => $this->header_config_draft,
            'footer_config' => $this->footer_config_draft,
        ]);
        
        // Clear cache
        cache()->forget("active_theme");
        cache()->forget("active_theme_routes");
    }

    /**
     * Get the active theme.
     */
    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Get available sections for this theme.
     */
    public function getAvailableSections(): array
    {
        $sectionRegistry = app('visual-editor.section');
        $themeSections = $this->sections()->where('is_enabled', true)->pluck('section_key')->toArray();

        return array_filter($sectionRegistry->all(), function($key) use ($themeSections) {
            return in_array($key, $themeSections);
        }, ARRAY_FILTER_USE_KEY);
    }
}
