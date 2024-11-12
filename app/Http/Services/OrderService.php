<?php

namespace App\Http\Services;

use App\Events\OrderCreated;
use App\Http\Interfaces\OrderInterface;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderService implements OrderInterface
{
    public function store($request)
    {
        // get auth user id
        $user_id = Auth::id();

        // store order
        $order = Order::create([
            'user_id' => $user_id,
        ]);

        // get products_ids with their quantities
        $products = $request->products;

        // Attach products to the order with quantity
        $total_price = 0;
        foreach ($products as $product) {
            $product_id = $product['product_id'];
            $quantity = $product['quantity'];
            $product_price = Product::find($product_id)?->price;
            $order_details_price = $product_price * $quantity;
            $order->products()->attach($product_id, ['quantity' => $quantity, 'price' => $order_details_price]);
            $total_price += $order_details_price;
        }

        // put total price in orders table
        $order->total_price = $total_price;
        $order->save();

        // send mail to user 
        event(new OrderCreated($order));

        // Return a localized message
        return response()->json([
            'message' => __('messages.order_created'),
            'data' => [],
        ], 201);
    }


    public function getOrderDetails($order_id)
    {
        $order = Order::find($order_id); // Find the specific order

        // Get all products for the order, including pivot data
        $products = $order->products;

        $productsArray = []; // Initialize an empty array to store the result

        foreach ($products as $product) {
            $productsArray[] = [
                'product_name' => $product->name,
                'quantity' => $product->pivot->quantity, // Pivot table column 'quantity'
                'price' => $product->pivot->price,       // Pivot table column 'price'
            ];
        }

        // Now $productsArray holds the products and their pivot data
        return response()->json([
            'message' => [],
            'data' => $productsArray
        ], 200);
    }
}
