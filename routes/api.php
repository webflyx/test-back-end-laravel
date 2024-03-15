<?php

use App\Http\Controllers\Api\V1\UserRegistrationController;
use App\Http\Middleware\RegisterValidToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {

    Route::get('/token', [UserRegistrationController::class, 'getRegisterToken']);

    Route::prefix('users')->group(function () {
        Route::post('/', [UserRegistrationController::class, 'registerUser'])->middleware(RegisterValidToken::class);
    });

});
