<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
    //
    protected $fillable = ['name', 'price', 'sign', 'show', 'package', 'position'];
}
