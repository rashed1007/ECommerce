<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\ProductInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public $productInterface;

    public function __construct(ProductInterface $p)
    {
        $this->productInterface = $p;
    }



    public function getAll(Request $request)
    {
        return $this->productInterface->getAll($request);
    }

    public function search(Request $request)
    {
        return $this->productInterface->search($request);
    }
}
