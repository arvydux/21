<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ceburek extends Model
{
    //
    protected $fillable = ['number', 'name', 'price', 'sign', 'show', 'category', 'package', 'position', 'subcategory_id'];

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
}
