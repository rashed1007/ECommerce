<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $guarded = [];


    public function scopeSearch(Builder $query, $name = null, $minPrice = null, $maxPrice = null, $price = null, $category = null,)
    {
        // Search by name if provided
        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        // Search by price range if provided
        if (!is_null($minPrice)) {
            $query->where('price', '>=', $minPrice);
        }
        if (!is_null($maxPrice)) {
            $query->where('price', '<=', $maxPrice);
        }

        // Apply category filter if provided
        if ($price) {
            $query->where('price', '<=', $price);
        }

        // Apply category filter if provided
        if ($category) {
            $query->where('category_id', $category); // Assuming products have a 'category_id' field
        }

        return $query;
    }

    public function orders()
    {
        return $this->belongsToMany('App\Models\Order', 'order_product', 'product_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }
}
