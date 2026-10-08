<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PotatoDish extends Model
{
    /** @use HasFactory<\Database\Factories\PotatoDishFactory> */
    use HasFactory;

    /**
     * Value stored in orders.category for potato dish order lines.
     */
    public const ORDER_CATEGORY = 5;

    public const HALF_PORTION_SUFFIX = ' ½';

    public const FULL_PORTION_SUFFIX = ' (pilna)';

    protected $fillable = ['name', 'price', 'half_price', 'sign', 'show', 'package', 'position', 'attention', 'left', 'subcategory_id'];

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function priceFor(bool $isHalfPortion): float
    {
        return (float) ($isHalfPortion ? $this->half_price : $this->price);
    }

    /**
     * Name shown in the cart and on the kitchen ticket, e.g. "Žemaičių blynai su kiauliena ½".
     */
    public function orderLabel(bool $isHalfPortion): string
    {
        return $this->name.($isHalfPortion ? self::HALF_PORTION_SUFFIX : self::FULL_PORTION_SUFFIX);
    }

    /**
     * Strip the portion suffix from an order label to get back the product name.
     */
    public static function nameFromOrderLabel(string $label): string
    {
        foreach ([self::HALF_PORTION_SUFFIX, self::FULL_PORTION_SUFFIX] as $suffix) {
            if (str_ends_with($label, $suffix)) {
                return substr($label, 0, -strlen($suffix));
            }
        }

        return $label;
    }
}
