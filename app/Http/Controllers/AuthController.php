<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\AuthInterface;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public $authInterface;

    public function __construct(AuthInterface $a)
    {
        $this->authInterface = $a;
    }

    public function register(RegisterRequest $request)
    {
        return $this->authInterface->register($request);
    }

    public function login(LoginRequest $request)
    {
        return $this->authInterface->login($request);
    }
}
