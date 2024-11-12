<?php

namespace App\Http\Interfaces;


interface ProductInterface
{
    public function getAll($request);

    public function search($request);
}
