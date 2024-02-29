<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // If Invalid email or password
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['status' => false ,'error' => 'Invalid email or password'], 401);
        }

        // Retrieve the authenticated user
        $user = Auth::user(); 

        // customize the user data you want to return in the response
        return response()->json(['status' => true ,'token' => $token, 'user' => $user], 200);
    }

}
