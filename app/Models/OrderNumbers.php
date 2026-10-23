<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderNumbers extends Model
{
    //
    protected $fillable = ['number', 'is_ready', 'by_phone', 'is_taken'];

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class, 'order_number_id');
    }

    public function getMinutesSinceCreatedAttribute()
    {
        return $this->created_at->diffInMinutes(Carbon::now());
    }
}
