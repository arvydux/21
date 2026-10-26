<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KoldunaiDish extends Model
{
    /** @use HasFactory<\Database\Factories\KoldunaiDishFactory> */
    use HasFactory;

    /**
     * Value stored in orders.category for "Koldūnai bei bulvytės" order lines.
     */
    public const ORDER_CATEGORY = 6;

    protected $fillable = ['number', 'name', 'price', 'sign', 'show', 'package', 'position', 'attention', 'left', 'subcategory_id', 'for_kitchen'];

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
}
