<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtherProduct extends Model
{
    //
    protected $fillable = ['number', 'name', 'price', 'show', 'takeaway', 'package', 'position', 'attention', 'left', 'subcategory_id', 'for_kitchen'];

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
}
