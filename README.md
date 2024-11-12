# API Documentation

This documentation outlines the available endpoints for the Products and Orders APIs, including user registration and authentication.

## Products API

The Products API provides endpoints to retrieve and search for products with filtering and pagination capabilities.

### 1. Get All Products

- **URL**: `/products`
- **Method**: `GET`
- **Description**: Retrieves a list of all products. Supports pagination, price filtering, and category filtering. Results are cached for 10 minutes.

#### Parameters

| Parameter   | Type     | Description                                   |
|-------------|----------|-----------------------------------------------|
| `page`      | Integer  | The page number for pagination (default: 1)  |
| `per_page`  | Integer  | Number of products per page                   |
| `price`     | Float    | Filter products by price                      |
| `category`  | String   | Filter products by category                   |

#### Response

- **200 OK**
    ```json
    {
      "message": [],
      "data": {
        "current_page": 1,
        "data": [
          {
            "id": 1,
            "name": "Product 1",
            "price": 100,
            "category": "Electronics"
          },
          {
            "id": 2,
            "name": "Product 2",
            "price": 150,
            "category": "Books"
          }
        ],
        "total": 100,
        "per_page": 10
      }
    }
    ```

---

### 2. Search Products

- **URL**: `/products/search`
- **Method**: `GET`
- **Description**: Search for products by name and filter by price range. Supports pagination.

#### Parameters

| Parameter   | Type     | Description                              |
|-------------|----------|------------------------------------------|
| `name`      | String   | Search products by name                  |
| `min_price` | Float    | Minimum price for filtering products      |
| `max_price` | Float    | Maximum price for filtering products      |
| `page`      | Integer  | The page number for pagination (default: 1) |

#### Response

- **200 OK**
    ```json
    {
      "message": [],
      "data": {
        "current_page": 1,
        "data": [
          {
            "id": 2,
            "name": "Product B",
            "price": 150
          },
          {
            "id": 3,
            "name": "Product C",
            "price": 200
          }
        ],
        "total": 2,
        "per_page": 10
      }
    }
    ```

---

# Orders API Documentation

The Orders API handles the creation of new orders and retrieving order details.

## 1. Create Order

- **URL**: `/orders`
- **Method**: `POST`
- **Description**: Creates a new order for the authenticated user. Each order can contain multiple products with their quantities.

### Request Body

```json
{
  "products": [
    { "product_id": 1, "quantity": 2 },
    { "product_id": 2, "quantity": 1 }
  ]
}


Response
{
  "message": "Order created successfully",
  "data": []
}



2. Get Order Details
URL: /orders/{id}
Method: GET
Description: Retrieves the details of a specific order, including the products, quantities, and total price.

Response
{
  "message": [],
  "data": {
    "id": 1,
    "user_id": 2,
    "total_price": 300,
    "products": [
      {
        "id": 1,
        "name": "Product 1",
        "price": 100,
        "quantity": 2
      },
      {
        "id": 2,
        "name": "Product 2",
        "price": 150,
        "quantity": 1
      }
    ]
  }
}

