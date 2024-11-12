<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\OrderInterface;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public $orderInterface;

    public function __construct(OrderInterface $o)
    {
        $this->orderInterface = $o;
    }



    public function store(StoreOrderRequest $request)
    {
        return $this->orderInterface->store($request);
    }


    public function getOrderDetails($order_id)
    {
        return $this->orderInterface->getOrderDetails($order_id);
    }
}
