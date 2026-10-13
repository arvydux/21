<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subcategory extends Model
{
    /**
     * Name of the default subcategory that lists every product of its category.
     */
    public const DEFAULT_NAME = 'Visi';

    protected $fillable = ['name', 'category_id', 'position'];

    public function isDefault(): bool
    {
        return $this->name === self::DEFAULT_NAME;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
