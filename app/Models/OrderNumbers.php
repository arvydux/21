<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OrderNumbers extends Model
{
    //
    protected $fillable = ['number', 'is_ready', 'by_phone', 'is_taken'];

    public function getMinutesSinceCreatedAttribute()
    {
        return $this->created_at->diffInMinutes(Carbon::now());
    }
}
