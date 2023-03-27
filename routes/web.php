<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Examples\Example1Controller;
use App\Http\Controllers\Examples\Example2Controller;
use App\Http\Controllers\Examples\Example3Controller;

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
    return view('welcome');
});

Route::get('/sign-in/{user}', [AuthController::class, 'auth']);
Route::get('/sign-out', [AuthController::class, 'logout']);

Route::get('/example1', [Example1Controller::class, 'index'])->middleware('can:example1.viewAny');
Route::get('/example2', [Example2Controller::class, 'show']);
Route::any('/example3/{frm}', [Example3Controller::class, 'update']);
