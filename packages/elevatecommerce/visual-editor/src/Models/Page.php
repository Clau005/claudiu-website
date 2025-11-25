<?php

namespace ElevateCommerce\VisualEditor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'theme_id',
        'name',
        'slug',
        'type',
        'context_key',
        'route_pattern',
        'is_published',
        'draft_config',
        'published_config',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'draft_config' => 'array',
        'published_config' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Get the theme that owns this page.
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    /**
     * Publish the draft configuration.
     */
    public function publish(): void
    {
        $this->update([
            'published_config' => $this->draft_config,
            'is_published' => true,
            'published_at' => now(),
        ]);

        // Clear route cache to regenerate routes
        cache()->forget("theme_pages:{$this->theme_id}");
        cache()->forget('active_theme_routes');
        cache()->forget("page:{$this->slug}");
    }

    /**
     * Unpublish the page.
     */
    public function unpublish(): void
    {
        $this->update([
            'is_published' => false,
        ]);
    }

    /**
     * Revert to published configuration.
     */
    public function revertToPublished(): void
    {
        $this->update([
            'draft_config' => $this->published_config,
        ]);
    }

    /**
     * Get the configuration to render (published if available, otherwise draft).
     */
    public function getRenderConfig(): ?array
    {
        return $this->is_published ? $this->published_config : $this->draft_config;
    }

    /**
     * Update draft configuration.
     */
    public function updateDraft(array $config): void
    {
        $this->update([
            'draft_config' => $config,
        ]);
    }

    /**
     * Add a section to draft configuration.
     */
    public function addSection(string $sectionKey, array $settings = [], ?int $position = null): void
    {
        $config = $this->draft_config ?? [];
        
        $section = [
            'key' => $sectionKey,
            'settings' => $settings,
            'id' => \Illuminate\Support\Str::uuid()->toString(),
        ];

        if ($position !== null) {
            array_splice($config, $position, 0, [$section]);
        } else {
            $config[] = $section;
        }

        $this->updateDraft($config);
    }

    /**
     * Remove a section from draft configuration.
     */
    public function removeSection(string $sectionId): void
    {
        $config = $this->draft_config ?? [];
        
        $config = array_filter($config, function($section) use ($sectionId) {
            return ($section['id'] ?? null) !== $sectionId;
        });

        $this->updateDraft(array_values($config));
    }

    /**
     * Update a section in draft configuration.
     */
    public function updateSection(string $sectionId, array $settings): void
    {
        $config = $this->draft_config ?? [];
        
        foreach ($config as &$section) {
            if (($section['id'] ?? null) === $sectionId) {
                $section['settings'] = array_merge($section['settings'] ?? [], $settings);
                break;
            }
        }

        $this->updateDraft($config);
    }

    /**
     * Reorder sections in draft configuration.
     */
    public function reorderSections(array $sectionIds): void
    {
        $config = $this->draft_config ?? [];
        $ordered = [];

        foreach ($sectionIds as $id) {
            foreach ($config as $section) {
                if (($section['id'] ?? null) === $id) {
                    $ordered[] = $section;
                    break;
                }
            }
        }

        $this->updateDraft($ordered);
    }
}
