<?php

namespace App\Http\Interfaces;


interface OrderInterface
{
    public function store($request);

    public function getOrderDetails($order_id);
}
