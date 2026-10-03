{{--
    A host page that already shows somebody, plus the assignment card.

    This is the whole integration for the widget: one directive. It is
    duck-typed on getOwner(), which comes from the HasPermissions trait that
    access-rules asks you to put on the model, so the package needs to know
    nothing about App\Models\User. The owner row is created on demand — a user
    has none until something is granted to them.

    The card renders nothing at all when accessUi.routes are switched off or the
    inherit screen is disabled, so this page stays valid either way.

    The counts it shows are worth reading together: "nothing assigned" with a
    non-zero effective total means somebody granted this account something
    directly, which a list of roles would never reveal.
--}}
@extends('layouts.admin')

@section('content')

    <h1>{{ $user->name }}</h1>

    <dl>
        <dt>ID</dt>
        <dd>{{ $user->getKey() }}</dd>

        <dt>Email</dt>
        <dd>{{ $user->email }}</dd>

        <dt>Access-rules owner id</dt>
        <dd>{{ $owner_id }}</dd>
    </dl>

    {{-- The card of wnikk/laravel-access-ui. It renders nothing while the routes of the panel are off. --}}
    @accessUiWidget(['owner' => $user, 'title' => __('Access for this account')])

    <p style="margin-top:2rem;opacity:.7">
        <a href="{{ url('/users') }}">All users</a>
        · <a href="{{ url('/sign-in/'.$user->getKey()) }}">sign in as {{ $user->name }}</a>
        · <a href="{{ url('/example8') }}">then open example 8</a> to see what the card granted.
    </p>

@endsection
