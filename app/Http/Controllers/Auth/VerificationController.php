<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\Notifications\VerifyEmailWithOtp;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use App\Models\User;
use App\Models\Otp;

class VerificationController extends Controller
{

    public function showVerification()
    {
        return view('auth.verify');
    }

    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function verify(Request $request , $otp)
    {
        $token = $request->bearerToken();

        $decoded = JWTAuth::setToken($token)->getPayload();

        $user_id = $decoded['sub'];

        $user = User::findOrFail($user_id);

        $otpData = Otp::where("user_id", $user_id)->orderBy('created_at', 'desc')->first();

        if (! $otpData || $otpData->otp !== $otp) {

            return response()->json([
                'status' => false,
                'message' => 'Invalid Otp',
            ]);
        }

        if(now()->gt($otpData->expires_at)){
            
            return response()->json([
                'status' => false,
                'message' => 'Otp Expired',
            ]);
        }

        $user->markEmailAsVerified();

        $otpData->delete();

        event(new Verified($user));

        return  response()->json([
            'status' => true,
            'message' => 'User Verified successfully',
        ]);
    }

    public function resendOtp(Request $request)
    {
        $token = $request->bearerToken();

        $decoded = JWTAuth::setToken($token)->getPayload();

        $user_id = $decoded['sub'];

        $user = User::findOrFail($user_id);

        $otp = rand(1000, 9999);

        $otpData = Otp::create([
            'user_id' => $user->id,
            'otp' => strval($otp),
            'expires_at' => now()->addMinutes(2), 
        ]);

        // Send Otp
        Mail::to($user->email)->send(new OtpMail($otp , $user->firstname));

        return  response()->json([
            'status' => true,
            'message' => 'Otp sent successfully',
        ]);
    }
}
