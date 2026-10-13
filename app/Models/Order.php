<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['name', 'toppings', 'amount', 'takeaway', 'order_price', 'category', 'package', 'comments'];

    /**
     * Whether this cart line may only be sold to adults (alcohol).
     */
    public function isForAdults(): bool
    {
        return (int) $this->category === AlcoholicDrink::ORDER_CATEGORY;
    }
}
