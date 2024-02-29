<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Revoking all of the user's tokens
        $request->user()->tokens()->delete(); 

        // You can redirect to any URL after logout
        Auth::logout();

        return response()->json(['status' => true ,'message' => 'user logout successfully'], 200);
    }
}
