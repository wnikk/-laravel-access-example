<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Examples\Example1Controller;
use App\Http\Controllers\Examples\Example2Controller;
use App\Http\Controllers\Examples\Example3Controller;
use App\Http\Controllers\Examples\Example4Controller;
use App\Http\Controllers\Examples\Example5Controller;
use App\Http\Controllers\Examples\Example6Controller;
use App\Http\Controllers\Examples\Example7Controller;
use App\Http\Controllers\Examples\UserRulesController;
use App\Http\Controllers\Examples\UserProfileController;

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

Route::apiResource('example4', Example4Controller::class)->parameters([
    'example4' => 'news'
]);

Route::apiResource('example5', Example5Controller::class)->parameters([
    'example5' => 'news'
]);

Route::any('/example6/{news}', [Example6Controller::class, 'update']);

Route::get('/example7/{news}', [Example7Controller::class, 'index']);

Route::get('/rules', [UserRulesController::class, 'main'])->name('userRoles');
Route::any('/rules/rule-{action}/{id?}', [UserRulesController::class, 'rules'])->name('userRoles.rule');
Route::any('/rules/role-{action}/{id?}', [UserRulesController::class, 'roles'])->name('userRoles.role');
Route::any('/rules/inherit-{action}/{owner}/{id?}', [UserRulesController::class, 'inherit'])->name('userRoles.inherit');
Route::any('/rules/permission-{action}/{owner}/{id?}', [UserRulesController::class, 'permission'])->name('userRoles.permission');
Route::get('/user', [UserProfileController::class, 'edit']);
