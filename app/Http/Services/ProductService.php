<?php

namespace App\Http\Services;

use App\Http\Interfaces\ProductInterface;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductService implements ProductInterface
{
    public function getAll($request)
    {
        // Determine the current page and filters from the request
        $page = request()->get('page', 1); // Default to page 1
        $perPage = request()->get('per_page');; // Set the number of products per page
        $price = request()->get('price'); // Price filter
        $category = request()->get('category'); // Category filter

        // Create a cache key that includes filters and the page number
        $cacheKey = "products_page_{$page}_price_{$price}_category_{$category}";

        // Cache the products based on the page number and filters for 10 minutes
        $products = Cache::remember($cacheKey, 600, function () use ($price, $category, $perPage) {

            $products = Product::search(null, null, null, $price, $category)->paginate($perPage);
            return $products;
        });

        // Return the paginated and filtered products
        return response()->json([
            'message' => [],
            'data' => $products
        ], 200);
    }


    public function search($request)
    {
        $name = $request->input('name');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        $products = Product::search($name, $minPrice, $maxPrice)->paginate(10); // Paginate results

        return response()->json([
            'message' => [],
            'data' => $products,
        ], 200);
    }
}
