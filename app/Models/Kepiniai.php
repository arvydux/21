<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kepiniai extends Model
{
    protected $table = 'kepiniai';

    protected $fillable = ['name', 'price', 'show', 'takeaway', 'package', 'position', 'attention', 'left', 'subcategory_id'];

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
}
