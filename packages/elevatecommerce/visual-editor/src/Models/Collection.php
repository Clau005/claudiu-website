<?php

namespace ElevateCommerce\VisualEditor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use ElevateCommerce\VisualEditor\Traits\Taggable;

class Collection extends Model
{
    use SoftDeletes, Taggable;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'image',
        'type',
        'conditions',
        'page_id',
        'metafields',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'conditions' => 'array',
        'metafields' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the page template for this collection.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get all items in this collection (polymorphic).
     * Usage: $collection->items(Product::class)->get()
     */
    public function items(string $type = null): MorphToMany
    {
        $relation = $this->morphedByMany(
            $type ?? Model::class,
            'collectable',
            'collectables'
        )->withPivot('position')
         ->orderBy('collectables.position');

        return $relation;
    }

    /**
     * Add an item to this collection.
     */
    public function addItem(Model $item, int $position = null): void
    {
        if ($position === null) {
            // Get next position
            $position = $this->collectables()->max('position') + 1;
        }

        $this->collectables()->create([
            'collectable_type' => get_class($item),
            'collectable_id' => $item->id,
            'position' => $position,
        ]);
    }

    /**
     * Remove an item from this collection.
     */
    public function removeItem(Model $item): void
    {
        $this->collectables()
            ->where('collectable_type', get_class($item))
            ->where('collectable_id', $item->id)
            ->delete();
    }

    /**
     * Get the collectables pivot records.
     */
    public function collectables()
    {
        return $this->hasMany(Collectable::class);
    }

    /**
     * Publish this collection.
     */
    public function publish(): void
    {
        $this->update([
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    /**
     * Unpublish this collection.
     */
    public function unpublish(): void
    {
        $this->update([
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
