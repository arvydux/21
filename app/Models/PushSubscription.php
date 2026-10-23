<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A browser that asked the online board to be notified once one order is ready.
 */
class PushSubscription extends Model
{
    /** @use HasFactory<\Database\Factories\PushSubscriptionFactory> */
    use HasFactory;

    protected $fillable = ['order_number_id', 'endpoint', 'public_key', 'auth_token'];

    public function orderNumber(): BelongsTo
    {
        return $this->belongsTo(OrderNumbers::class, 'order_number_id');
    }
}
