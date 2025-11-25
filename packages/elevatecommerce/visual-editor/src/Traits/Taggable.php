<?php

namespace ElevateCommerce\VisualEditor\Traits;

use ElevateCommerce\VisualEditor\Models\Tag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Taggable
{
    /**
     * Get all tags for this model.
     *
     * @return MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Attach tags to the model.
     *
     * @param array|string $tags Tag names or IDs
     * @return void
     */
    public function attachTags(array|string $tags): void
    {
        $tags = is_string($tags) ? explode(',', $tags) : $tags;
        $tagIds = [];

        foreach ($tags as $tag) {
            $tag = trim($tag);
            
            if (empty($tag)) {
                continue;
            }

            // If numeric, assume it's an ID
            if (is_numeric($tag)) {
                $tagIds[] = $tag;
            } else {
                // Find or create tag by name
                $tagModel = Tag::findOrCreateByName($tag);
                $tagIds[] = $tagModel->id;
            }
        }

        $this->tags()->syncWithoutDetaching($tagIds);
    }

    /**
     * Detach tags from the model.
     *
     * @param array|string|null $tags Tag names or IDs, null to detach all
     * @return void
     */
    public function detachTags(array|string|null $tags = null): void
    {
        if ($tags === null) {
            $this->tags()->detach();
            return;
        }

        $tags = is_string($tags) ? explode(',', $tags) : $tags;
        $tagIds = [];

        foreach ($tags as $tag) {
            $tag = trim($tag);
            
            if (is_numeric($tag)) {
                $tagIds[] = $tag;
            } else {
                $tagModel = Tag::where('name', $tag)->orWhere('slug', $tag)->first();
                if ($tagModel) {
                    $tagIds[] = $tagModel->id;
                }
            }
        }

        $this->tags()->detach($tagIds);
    }

    /**
     * Sync tags (replace all existing tags).
     *
     * @param array|string $tags Tag names or IDs
     * @return void
     */
    public function syncTags(array|string $tags): void
    {
        $tags = is_string($tags) ? explode(',', $tags) : $tags;
        $tagIds = [];

        foreach ($tags as $tag) {
            $tag = trim($tag);
            
            if (empty($tag)) {
                continue;
            }

            if (is_numeric($tag)) {
                $tagIds[] = $tag;
            } else {
                $tagModel = Tag::findOrCreateByName($tag);
                $tagIds[] = $tagModel->id;
            }
        }

        $this->tags()->sync($tagIds);
    }

    /**
     * Check if model has a specific tag.
     *
     * @param string|int $tag Tag name, slug, or ID
     * @return bool
     */
    public function hasTag(string|int $tag): bool
    {
        if (is_numeric($tag)) {
            return $this->tags()->where('tag_id', $tag)->exists();
        }

        return $this->tags()
            ->where('name', $tag)
            ->orWhere('slug', $tag)
            ->exists();
    }

    /**
     * Check if model has any of the given tags.
     *
     * @param array $tags
     * @return bool
     */
    public function hasAnyTag(array $tags): bool
    {
        foreach ($tags as $tag) {
            if ($this->hasTag($tag)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if model has all of the given tags.
     *
     * @param array $tags
     * @return bool
     */
    public function hasAllTags(array $tags): bool
    {
        foreach ($tags as $tag) {
            if (!$this->hasTag($tag)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Scope to filter models by tag.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $tags
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithTag($query, string|array $tags)
    {
        $tags = is_array($tags) ? $tags : [$tags];

        return $query->whereHas('tags', function ($q) use ($tags) {
            $q->whereIn('name', $tags)
                ->orWhereIn('slug', $tags);
        });
    }

    /**
     * Scope to filter models by any of the given tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $tags
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAnyTag($query, array $tags)
    {
        return $query->whereHas('tags', function ($q) use ($tags) {
            $q->whereIn('name', $tags)
                ->orWhereIn('slug', $tags);
        });
    }

    /**
     * Scope to filter models by all of the given tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $tags
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllTags($query, array $tags)
    {
        foreach ($tags as $tag) {
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('name', $tag)
                    ->orWhere('slug', $tag);
            });
        }

        return $query;
    }

    /**
     * Get tag names as comma-separated string.
     *
     * @return string
     */
    public function getTagNamesAttribute(): string
    {
        return $this->tags->pluck('name')->implode(', ');
    }
}
