<?php

namespace ElevateCommerce\VisualEditor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from name
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Get all taggable items of a specific type.
     *
     * @param string $type Model class name
     * @return MorphToMany
     */
    public function taggables(string $type): MorphToMany
    {
        return $this->morphedByMany($type, 'taggable');
    }

    /**
     * Scope to find tags by name or slug.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $identifier
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByIdentifier($query, string $identifier)
    {
        return $query->where('name', $identifier)
            ->orWhere('slug', $identifier);
    }

    /**
     * Find or create a tag by name.
     *
     * @param string $name
     * @param array $attributes
     * @return static
     */
    public static function findOrCreateByName(string $name, array $attributes = []): static
    {
        $tag = static::where('name', $name)->first();

        if (!$tag) {
            $tag = static::create(array_merge(['name' => $name], $attributes));
        }

        return $tag;
    }

    /**
     * Get tag usage count across all models.
     *
     * @return int
     */
    public function getUsageCountAttribute(): int
    {
        return DB::table('taggables')
            ->where('tag_id', $this->id)
            ->count();
    }
}
