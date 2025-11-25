<?php

namespace ElevateCommerce\VisualEditor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThemeSection extends Model
{
    protected $fillable = [
        'theme_id',
        'section_key',
        'order',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the theme that owns this section.
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    /**
     * Get the section configuration from registry.
     */
    public function getSectionConfig(): ?array
    {
        return app('visual-editor.section')->get($this->section_key);
    }
}
