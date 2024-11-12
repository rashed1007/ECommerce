<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Events\OrderCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase; // This will reset the database after each test

    /**
     * Test the store method for creating an order.
     */
    public function test_store_order_successfully()
    {
        // Step 1: Fake events to prevent actual firing
        Event::fake(); // Prevent the OrderCreated event from firing

        // Step 2: Create a user and authenticate them
        $user = User::factory()->create();
        $this->actingAs($user); // Authenticate the user for the request

        // Step 3: Create products for the order
        $product1 = Product::factory()->create(['price' => 100]); // Product with price 100
        $product2 = Product::factory()->create(['price' => 200]); // Product with price 200

        // Step 4: Define the request data (this mimics the form input)
        $requestData = [
            'products' => [
                ['product_id' => $product1->id, 'quantity' => 2], // 2 x 100
                ['product_id' => $product2->id, 'quantity' => 1], // 1 x 200
            ]
        ];

        // Step 5: Make a POST request to the store method (mimicking API call)
        $response = $this->postJson(route('orders.store'), $requestData);

        // Step 6: Check the response status
        $response->assertStatus(201);

        // Step 7: Verify the data in the database
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_price' => 400, // (2 * 100) + (1 * 200)
        ]);

        // Verify order_product table for attached products
        $this->assertDatabaseHas('order_product', [
            'order_id' => Order::first()->id, // Get the first order
            'product_id' => $product1->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('order_product', [
            'order_id' => Order::first()->id,
            'product_id' => $product2->id,
            'quantity' => 1,
        ]);

        // Step 8: Assert the event was dispatched
        Event::assertDispatched(OrderCreated::class);

        // Step 9: Verify the response structure and content
        $response->assertJson([
            'message' => __('messages.order_created'),
            'data' => [],
        ]);
    }

    public function test_get_order_details_successfully()
    {
        // Step 1: Create a user and authenticate them
        $user = User::factory()->create();
        $this->actingAs($user); // Authenticate the user for the request

        // Step 2: Create products and an order
        $product1 = Product::factory()->create(['price' => 100]);
        $product2 = Product::factory()->create(['price' => 200]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total_price' => 400,
        ]);

        // Attach products to the order
        $order->products()->attach($product1->id, ['quantity' => 2, 'price' => 200]); // 2 * 100
        $order->products()->attach($product2->id, ['quantity' => 1, 'price' => 200]); // 1 * 200

        // Step 3: Make a GET request to the getOrderDetails method
        $response = $this->getJson(route('orders.getOrderDetails', ['id' => $order->id]));

        // Step 4: Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $order->id,
                    'total_price' => 400,
                    'products' => [
                        [
                            'id' => $product1->id,
                            'quantity' => 2,
                            'price' => 200,
                        ],
                        [
                            'id' => $product2->id,
                            'quantity' => 1,
                            'price' => 200,
                        ],
                    ],
                ],
            ]);
    }
}
