{{--
    Stand-in for a login screen.

    This sandbox authenticates by user id through /sign-in/{user} rather than a
    form, but the `auth` middleware still redirects guests to a route named
    "login" — without one it raises RouteNotFoundException and the panel answers
    500 instead of sending the visitor anywhere. So the name has to exist, and
    this is the smallest honest thing to put behind it.

    Replace with a real login screen (or a starter kit) and keep the name.
--}}
@extends('layouts.admin')

@section('content')

    <h1>Sign in</h1>

    <p>Seeded accounts. Ann holds the role <code>root</code> and is a manager in team North, Bob is a manager in team South, users 3-5 hold nothing:</p>

    <ul>
        @foreach ($users as $user)
            <li>
                <a href="{{ url('/sign-in/'.$user->getKey()) }}">{{ $user->name }}</a>
                <small style="opacity:.7">({{ $user->email }})</small>
            </li>
        @endforeach
    </ul>

    @if ($users->isEmpty())
        <p>No users yet. Run <code>php artisan migrate:fresh --seed</code>.</p>
    @endif

@endsection
