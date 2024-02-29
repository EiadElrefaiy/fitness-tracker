<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\VerificationController;

use App\Http\Controllers\User\UpdateUserController;
use App\Http\Controllers\User\DeleteUserController;
use App\Http\Controllers\User\ReadUserController;
use App\Http\Controllers\User\GetAllUsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('register', [RegisterController::class, 'create']);

Route::post('login', [LoginController::class, 'login']);
    

Route::group(['middleware' => ['api' , 'jwt:api-user'] ,'prefix' =>'user'] , function(){

    Route::get('email/verify/{otp}', [VerificationController::class, 'verify']);

    Route::post('email/resend', [VerificationController::class, 'resendOtp']);

    Route::group(['middleware' => ['email.verified']] , function(){
    // Logout User
    Route::post('logout', [LogoutController::class, 'logout']);

    // Update User
    Route::match(['post', 'put'], 'update', [UpdateUserController::class, 'update']);

    // Read All Users
    Route::get('get-all', [GetAllUsersController::class, 'index']);

    // Read User
    Route::get('get', [ReadUserController::class, 'show']);

    // Delete User
    Route::delete('delete', [DeleteUserController::class, 'delete']);
   });
});
