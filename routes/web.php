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
use App\Http\Controllers\Examples\Example8Controller;
use App\Http\Controllers\Examples\Example9Controller;
use App\Http\Controllers\Examples\Example10Controller;
use App\Http\Controllers\Examples\Example11Controller;
use App\Http\Controllers\Examples\Example12Controller;
use App\Http\Controllers\Examples\Example13Controller;
use App\Http\Controllers\Examples\Example14Controller;
use App\Http\Controllers\Examples\Example15Controller;
use App\Http\Controllers\Examples\UserProfileController;
use App\Http\Controllers\Examples\UsersController;


Route::get('/', function () {
    $users = \App\Models\User::orderBy('id')->get();

    // Owners each user inherits from, by name: "RootAdmin role, Managers, North"
    $roles = $users->mapWithKeys(fn ($user) => [$user->getKey() => $user->access()->record()?->inheritance()->with('ownerParent')->get()
        ?->map(fn ($link) => $link->ownerParent?->name ?? $link->ownerParent?->original_id)->filter()->implode(', ') ?: null]);

    return view('welcome', [
        'users'     => $users,
        'roles'     => $roles,
        'scenarios' => array_map(fn (array $s) => $s['article'].': '.$s['what'], app(Example9Controller::class)->scenarios()),
        'catalog'   => array_map(fn (array $s) => $s['what'], app(Example15Controller::class)->scenarios()),
    ]);
});

/*
|--------------------------------------------------------------------------
| Test bench of wnikk/laravel-access-rules 3.x
|--------------------------------------------------------------------------
|
| Seed first: php artisan migrate:fresh --seed
|
|   /sign-in/1   Ann: role "root" of the first tutorial, manager of the shop, team North
|   /sign-in/2   Bob: manager of the shop, team South
|   /sign-in/3   a user that holds nothing
|   /sign-out
|
| Examples 1-7 follow docs/tutorial-basic-step-by-step.md (RBAC, as it was in 2.x).
| Examples 8-15 follow docs/tutorial-abac-step-by-step.md (ABAC, new in 3.x).
|
| The panel of wnikk/laravel-access-ui 3.x is at /access-control (config accessUi.routes).
| /users lists every account; /user/{id} is a profile with the assignment card of the panel.
|
*/

// The `auth` middleware redirects guests to a route named "login". Without one
// it throws RouteNotFoundException and a guest gets a 500 instead of a page.
Route::get('/login', function () {
    return view('login', ['users' => \App\Models\User::orderBy('id')->get()]);
})->name('login');

Route::get('/sign-in/{user}', [AuthController::class, 'auth']);
Route::get('/sign-out', [AuthController::class, 'logout']);

// The language of the session; the panel and its card follow it. See App\Http\Middleware\SetLocale.
Route::get('/lang/{locale}', function (string $locale) {
    abort_unless(in_array($locale, \App\Http\Middleware\SetLocale::LOCALES, true), 404);
    session(['locale' => $locale]);

    return redirect()->back();
});

Route::get('/user/{user?}', [UserProfileController::class, 'edit']);
Route::get('/users', [UsersController::class, 'index'])->middleware(['auth', 'can:manage-access']);

/*
| Examples 1-7: RBAC
*/

// #1 the check lives in the route
Route::get('/example1', [Example1Controller::class, 'index'])->middleware('can:example1.viewAny');

// #2 the check lives in the action
Route::get('/example2', [Example2Controller::class, 'show']);

// #3 options of one rule: /example3/name, /example3/email, /example3/password
Route::any('/example3/{frm}', [Example3Controller::class, 'update']);

// #4 authorizeResource() with global rules "viewAny", "view", "create", "update", "delete"
Route::apiResource('example4', Example4Controller::class)->parameters(['example4' => 'news']);

// #5 the same with rules of the controller: "Examples.Example5.viewAny" ...
Route::apiResource('example5', Example5Controller::class)->parameters(['example5' => 'news']);

// #6 the magic suffix ".self": only the author of the record
Route::any('/example6/{news}', [Example6Controller::class, 'update']);

// #7 a policy in 2.x, a condition in 3.x: news of the last 48 hours
Route::get('/example7', [Example7Controller::class, 'index']);
Route::get('/example7/{news}', [Example7Controller::class, 'show']);

/*
| Examples 8-14: ABAC
*/

// #8 one permission with a condition: the list and the record
Route::middleware('auth')->group(function () {
    Route::get('/example8', [Example8Controller::class, 'index']);
    Route::get('/example8/{order}', [Example8Controller::class, 'show']);
    Route::get('/example8/{order}/approve', [Example8Controller::class, 'approve']);
    Route::get('/example8/{order}/export/{format}', [Example8Controller::class, 'export']);
});

// #9 the scenarios of the article on the six orders. They change permissions, hence the guard.
Route::middleware(['auth', 'can:manage-access'])->group(function () {
    Route::get('/example9', [Example9Controller::class, 'index']);
    Route::get('/example9/{scenario}', [Example9Controller::class, 'show']);
});

// #10 three ways to ask, and a menu
Route::get('/example10', [Example10Controller::class, 'index']);

// #11 guests: open signed out
Route::get('/example11', [Example11Controller::class, 'index']);
Route::get('/example11/{product}', [Example11Controller::class, 'show']);

// #12 "why can't I see it?": add ?access_debug=1
Route::middleware('auth')->group(function () {
    Route::get('/example12', [Example12Controller::class, 'index']);
    Route::get('/example12/{order}', [Example12Controller::class, 'show']);
});

// #13 explain, lint and the audit log as data; #14 XACML
Route::middleware(['auth', 'can:manage-access'])->group(function () {
    Route::get('/example13/explain/{user}/{ability}/{order?}', [Example13Controller::class, 'explain']);
    Route::get('/example13/lint', [Example13Controller::class, 'lint']);
    Route::get('/example13/audit', [Example13Controller::class, 'audit']);

    Route::get('/example14', [Example14Controller::class, 'index']);
    Route::get('/example14/download', [Example14Controller::class, 'download']);
    Route::post('/example14/check', [Example14Controller::class, 'check']);
    Route::post('/example14/import', [Example14Controller::class, 'import']);

    // #15 polymorphic relations: example 18 of the article
    Route::get('/example15', [Example15Controller::class, 'index']);
    Route::get('/example15/{scenario}', [Example15Controller::class, 'show']);
});
