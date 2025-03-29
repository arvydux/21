<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = ['name', 'toppings', 'amount', 'takeaway', 'order_price'];
}
