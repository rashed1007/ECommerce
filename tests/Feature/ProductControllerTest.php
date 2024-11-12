<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getAll method with pagination, filters, and cache.
     */
    public function test_get_all_products_with_pagination_and_filters()
    {
        // Step 1: Mock the Cache facade
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(Product::factory()->count(5)->create());

        // Step 2: Store categories and Send a GET request with filters and pagination
        Category::factory()->count(5)->create();

        $response = $this->getJson(route('products.getAll', [
            'page' => 1,
            'per_page' => 5,
            'price' => 100,
            'category' => 2,
        ]));

        // Step 3: Assert that the response is successful
        $response->assertStatus(200);

        // Step 4: Assert that the structure of the response matches expectations
        $response->assertJsonStructure([
            'message',
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'price',
                        'category',
                        // Add more fields as needed
                    ]
                ]
            ]
        ]);
    }

    /**
     * Test caching in getAll method.
     */
    public function test_get_all_products_is_cached()
    {
        // Step 1: Set up mock data for products
        $products = Product::factory()->count(10)->create();

        // Step 2: Mock Cache::remember to ensure it's being called
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($products);

        // Step 3: Send GET request
        $response = $this->getJson(route('products.getAll', [
            'page' => 1,
            'per_page' => 10,
            'price' => null,
            'category' => null
        ]));

        // Step 4: Assert the response status and data
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'data' => $products->toArray()
                ]
            ]);
    }

    /**
     * Test search method with name and price filters.
     */
    public function test_search_products_by_name_and_price()
    {
        // Step 1: Create products with different names and prices
        $products = Product::factory()->createMany([
            ['name' => 'Product A', 'price' => 50],
            ['name' => 'Product B', 'price' => 150],
            ['name' => 'Product C', 'price' => 200],
        ]);

        // Step 2: Send search request with name and price filters
        $response = $this->getJson(route('products.search', [
            'name' => 'Product',
            'min_price' => 100,
            'max_price' => 200,
        ]));

        // Step 3: Assert the response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'price',
                        ]
                    ]
                ]
            ]);

        // Step 4: Assert that the correct products are returned (Product B and C)
        $responseData = $response->json('data.data');
        $this->assertCount(2, $responseData); // Ensure only 2 products are returned
        $this->assertEquals('Product B', $responseData[0]['name']);
        $this->assertEquals('Product C', $responseData[1]['name']);
    }

    /**
     * Test pagination works as expected in the search method.
     */
    public function test_search_products_with_pagination()
    {
        // Step 1: Create 30 products
        $products = Product::factory()->count(30)->create();

        // Step 2: Send search request with pagination
        $response = $this->getJson(route('products.search', [
            'name' => 'Product',
            'page' => 2,
            'min_price' => null,
            'max_price' => null,
        ]));

        // Step 3: Assert the response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'price',
                        ]
                    ],
                    'current_page',
                    'total',
                ]
            ]);

        // Step 4: Assert the correct number of products are returned for page 2
        $responseData = $response->json('data.data');
        $this->assertCount(10, $responseData); // Assuming 10 items per page
    }
}
