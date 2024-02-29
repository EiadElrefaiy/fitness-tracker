<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Traits\ValidationTrait;
use App\Traits\PasswordValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use GuzzleHttp\Client;
use Firebase\JWT\JWT;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UpdateUserController extends Controller
{
    public function update(Request $request)
    {    
        try {
        $token = $request->bearerToken();
        $decoded = JWTAuth::setToken($token)->getPayload();
        $user_id = $decoded['sub'];
        $user = User::find($user_id);

        $validator =  $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($user->id),],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);
        
    } catch (ValidationException $e) {
        // Return JSON response for validation errors
        return response()->json([
            'status' => false,
            'errors' => $e->errors()], 422);
    }        
        $user->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'age' => $request->age,
            'height' => $request->height,
            'weight' => $request->weight,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if($request->delete_image == 1){
            Storage::delete('public/images/users/'.$user->image);
            $user->update([
              'image' => null
        ]);
        }else{
            if ($request->hasFile('image')) {
                Storage::delete('public/images/users/'.$user->image);
                $fileName = time() . '.' . $request->file('image')->extension();
                $request->file('image')->storeAs('public/images/users', $fileName);
                $user->update([
                    'image' => $fileName,
                ]);
            }    
        }    

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully'
        ], 201);
    }
}