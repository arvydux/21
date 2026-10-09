<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlcoholicDrink extends Model
{
    /** @use HasFactory<\Database\Factories\AlcoholicDrinkFactory> */
    use HasFactory;

    /**
     * Value stored in orders.category for "Alkoholis" order lines.
     */
    public const ORDER_CATEGORY = 7;

    protected $fillable = ['number', 'name', 'price', 'show', 'package', 'position', 'attention', 'left', 'subcategory_id'];

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
}
