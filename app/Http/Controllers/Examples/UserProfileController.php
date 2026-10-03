<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserProfileController extends Controller
{
    /**
     * A host page carrying the access-ui assignment widget.
     *
     * Passing a different user is what makes the widget testable: grant a role
     * to user 2 here, then check with `$user->can(...)` that access-rules
     * actually resolved it. Looking at somebody else's access needs the same
     * ability that guards the panel — the widget writes inheritance links, so
     * reaching it for an arbitrary account would otherwise be a way around the
     * panel's own guard.
     */
    public function edit(?User $user = null)
    {
        abort_unless(Auth::check(), 403);

        /** @var User $self */
        $self = Auth::user();

        if ($user === null || $user->is($self)) {
            $user = $self;
        } else {
            Gate::authorize('manage-access');
        }

        return view('user-profile', [
            'user' => $user,

            // getOwner() comes from the access-rules HasPermissions trait and
            // creates the owner row on demand, so this is never null here.
            'owner_id' => $user->getOwner()->getKey(),
        ]);
    }
}
