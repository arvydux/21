<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['name', 'toppings', 'amount', 'takeaway', 'order_price', 'category', 'package', 'comments', 'for_kitchen'];

    /**
     * Whether this cart line may only be sold to adults (alcohol).
     */
    public function isForAdults(): bool
    {
        return (int) $this->category === AlcoholicDrink::ORDER_CATEGORY;
    }

    /**
     * Package price of one unit, charged only when the line is taken away.
     */
    public function chargedPackage(): float
    {
        return $this->takeaway ? (float) $this->package : 0.0;
    }
}
