<!DOCTYPE html>
<html>

<head>
    <title>Order Created</title>
</head>

<body>
    <h1>Your Order Has Been Placed!</h1>
    <p>Order ID: {{ $order->id }}</p>
    <p>Thank you for your order, {{ $order->user->name }}.</p>
</body>

</html>
