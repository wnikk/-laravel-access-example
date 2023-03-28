<?php

namespace App\Http\Controllers\Examples;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('user-profile', [
            'user'     => $user,
            'owner_id' => $user->getOwner()->getKey(),
        ]);
    }
}
