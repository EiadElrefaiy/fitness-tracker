<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class RegisterController extends Controller
{
    use RegistersUsers;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function create(Request $request)
    {
        // Retrieve data
        $data = $request->all();

        // Data Validation
        $validator = Validator::make($data, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    
        // If Validation failed
        if ($validator->fails()) {
            return response()->json(['status' => false,'error' => $validator->errors()], 422);
        }

        $fileName = null;
        if ($request->hasFile('image')) {
        $fileName = time() . '.' . $request->file('image')->extension();
        $request->file('image')->storeAs('public/images/users', $fileName);
        }

        // Validation passed, create the user
        $user = User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'age' => $data['age'],
            'height' => $data['height'],
            'weight' => $data['weight'],
            'email' => $data['email'],
            'image' => $fileName,
            'password' => Hash::make($data['password']),
        ]);
    
        // Generate JWT token for the newly registered user
        $token = JWTAuth::fromUser($user);

        // Generate Otp for Email Verification
        $otp = rand(1000, 9999);

        // Set the expiry time
        $otpData = Otp::create([
            'user_id' => $user->id,
            'otp' => strval($otp),
            'expires_at' => now()->addMinutes(2), 
        ]);

        // Send Otp
        Mail::to($user->email)->send(new OtpMail($otp , $user->firstname));

        // Json Response
        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
             'user' => $user ,
             'token' => $token
        ]);
      }
    }
