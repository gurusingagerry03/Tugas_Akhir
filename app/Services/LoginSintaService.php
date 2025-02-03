<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginSintaService
{
    public static function loginSinta(Request $request) {
        $theUrl     = 'http://apisinta.kemdikbud.go.id/consumer/login';
        $response= Http::post($theUrl, [
            'username'=> env('SINTA_USERNAME'),
            'password'=>  env('SINTA_PASSWORD')
        ]);

        $token = json_decode($response->getBody(), true);
        $token = $token['token'];

        return $token;
    }
}