<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    //
    protected $fillable = ['name', 'component_name', 'position'];

    /**
     * Give every new category its default "Visi" subcategory.
     */
    protected static function booted(): void
    {
        static::created(function (Category $category): void {
            Subcategory::firstOrCreate(
                ['category_id' => $category->id, 'name' => Subcategory::DEFAULT_NAME],
                ['position' => 0],
            );
        });
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class);
    }
}
