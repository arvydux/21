<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One kitchen line of a checked-out order, kept after the cart is cleared so the kitchen screen can
 * sum up what still has to be made.
 */
class KitchenOrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\KitchenOrderItemFactory> */
    use HasFactory;

    protected $fillable = ['order_number_id', 'name', 'amount', 'toppings', 'category', 'takeaway', 'comments'];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'toppings' => 'array',
            'category' => 'integer',
            'takeaway' => 'boolean',
        ];
    }

    public function orderNumber(): BelongsTo
    {
        return $this->belongsTo(OrderNumbers::class, 'order_number_id');
    }
}
