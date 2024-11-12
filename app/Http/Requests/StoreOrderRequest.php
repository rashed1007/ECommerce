<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'products' => 'required|array', // Ensure products is an array
            'products.*.product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id') // Ensure each product ID exists in the products table
            ],
            'products.*.quantity' => [
                'required',
                'integer',
                'min:1', // Ensure quantity is at least 1
                function ($attribute, $value, $fail) {
                    // Check if the requested quantity is available
                    $productId = explode('.', $attribute)[1]; // Get the product ID from the attribute
                    $product = Product::find($productId);
                    if ($product && $product->stock < $value) { // Assuming 'stock' is the available quantity field
                        return $fail('The quantity requested for product ' . $product->name . ' exceeds available stock.');
                    }
                },
            ],
        ];
    }
}
