<?php

use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $positions = Position::all();
    $users = User::orderBy('created_at', 'DESC')->paginate(5);

    return view('app', compact('users', 'positions'));
});
