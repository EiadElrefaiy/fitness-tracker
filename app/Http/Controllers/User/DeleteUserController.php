<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class DeleteUserController extends Controller
{
    public function delete(Request $request)
    {
        $token = $request->bearerToken();

        $decoded = JWTAuth::setToken($token)->getPayload();

        $user_id = $decoded['sub'];

        $user = User::findOrFail($user_id);

        $user->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'user deleted successfully',
        ]);
    }
}
