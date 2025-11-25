<?php

namespace ElevateCommerce\VisualEditor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Collectable extends Model
{
    protected $fillable = [
        'collection_id',
        'collectable_type',
        'collectable_id',
        'position',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    /**
     * Get the collection.
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    /**
     * Get the collectable model (polymorphic).
     */
    public function collectable(): MorphTo
    {
        return $this->morphTo();
    }
}
