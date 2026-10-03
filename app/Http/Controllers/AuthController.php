<?php
namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function auth(User $user)
    {
        Auth::login($user);

        return redirect('/')->with('status', "You are authorized as \"{$user->name}\" (ID: {$user->id})");
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/')->with('status', 'Your authorization is closed');
    }
}
