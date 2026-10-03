{{--
    The accounts of the sandbox. "Profile" opens the page with the assignment card of
    wnikk/laravel-access-ui for that account; "sign in as" switches the session to it, so what
    the card granted can be checked on the examples right away.
--}}
@extends('layouts.admin', ['title' => 'Users'])

@section('content')

    <h1>Users</h1>

    <p style="opacity:.7">
        Open a profile to see the assignment card of the panel: what the account inherits from, what may be
        assigned, and how much it ends up holding. Sign in as the account to check the result on the examples.
    </p>

    <table cellpadding="6" border="1" style="border-collapse:collapse">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Approval limit</th>
            <th>Inherits from</th>
            <th></th>
        </tr>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->getKey() }}</td>
                <td><a href="{{ url('/user/'.$user->getKey()) }}">{{ $user->name }}</a></td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->department_id }}</td>
                <td>{{ $user->approval_limit }}</td>
                <td>{{ $sources[$user->getKey()] ?? 'nothing' }}</td>
                <td>
                    <a href="{{ url('/user/'.$user->getKey()) }}">profile</a>
                    · <a href="{{ url('/sign-in/'.$user->getKey()) }}">sign in as</a>
                </td>
            </tr>
        @endforeach
    </table>

    @if ($users->isEmpty())
        <p>No users yet. Run <code>php artisan migrate:fresh --seed</code>.</p>
    @endif

@endsection
