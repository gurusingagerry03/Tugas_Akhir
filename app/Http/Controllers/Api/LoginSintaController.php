<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LoginSintaService;
use Illuminate\Http\Request;

class LoginSintaController extends Controller
{
    protected $loginSintaService;

    public function __construct(LoginSintaService $loginSintaService)
    {
        $this->loginSintaService = $loginSintaService;
    }

    public function login(Request $request)
    {
        return $this->loginSintaService->LoginSinta($request);
    }
}
