<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KoldunaiTopping extends Model
{
    /** @use HasFactory<\Database\Factories\KoldunaiToppingFactory> */
    use HasFactory;

    protected $fillable = ['name', 'price', 'sign', 'show', 'is_default', 'package', 'position', 'attention', 'left'];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }
}
