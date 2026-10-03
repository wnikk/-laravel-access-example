<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Models\User;

/**
 * Every account of the sandbox on one page, with a way to its profile and a way to sign in as it.
 *
 * The profile carries the assignment card of wnikk/laravel-access-ui, so this list is where a
 * check of the card starts: pick an account, look at what it inherits from, grant or remove,
 * then sign in as it and open an example. Reading the profile of somebody else needs the same
 * ability that guards the panel, and so does this list.
 */
class UsersController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id')->get();

        // What each account inherits from, by name, read from the tables of the core: the card
        // on the profile shows the same list and lets the administrator change it.
        $sources = $users->mapWithKeys(fn (User $user) => [
            $user->getKey() => $user->access()->record()?->inheritance()->with('ownerParent')->get()
                ?->map(fn ($link) => $link->ownerParent?->name ?? $link->ownerParent?->original_id)->filter()->implode(', ') ?: null,
        ]);

        return view('users', ['users' => $users, 'sources' => $sources]);
    }
}
