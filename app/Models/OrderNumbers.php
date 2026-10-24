<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class OrderNumbers extends Model
{
    /** Length of the random code in the personal order link printed on the receipt. */
    public const TOKEN_LENGTH = 10;

    protected $fillable = ['number', 'is_ready', 'by_phone', 'is_taken'];

    protected static function booted(): void
    {
        static::creating(function (OrderNumbers $order): void {
            $order->token ??= Str::random(self::TOKEN_LENGTH);
        });
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class, 'order_number_id');
    }

    /**
     * Where the customer can follow this order on their phone.
     */
    public function statusUrl(): string
    {
        return 'https://'.config('online-orders.public_host').'/online/'.$this->token;
    }

    /**
     * @return 'preparing'|'ready'|'taken'
     */
    public function status(): string
    {
        return match (true) {
            (bool) $this->is_taken => 'taken',
            (bool) $this->is_ready => 'ready',
            default => 'preparing',
        };
    }

    public function getMinutesSinceCreatedAttribute()
    {
        return $this->created_at->diffInMinutes(Carbon::now());
    }
}
