<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class GetAllUsersController extends Controller
{
    public function index()
    {
        $users = User::get();

        return response()->json([
            'users' => $users,
        ]);
    }
}
