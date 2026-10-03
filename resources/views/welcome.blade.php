{{--
    The front page of the test bench: who is signed in, and every route of the examples.

    Routes that answer JSON are plain links. Routes that need POST or another verb get a form
    or a note with the verb. Links that the signed in user is not permitted to open are shown
    anyway: a 403 is part of what the examples show.
--}}
@extends('layouts.admin', ['title' => 'Access rules 3.x: test bench'])

@section('content')

    <h1>wnikk/laravel-access-rules {{ \Composer\InstalledVersions::getPrettyVersion('wnikk/laravel-access-rules') }}</h1>

    @auth
        <p>
            Signed in as <strong>{{ auth()->user()->name }}</strong>
            (id {{ auth()->id() }}, {{ auth()->user()->email }}),
            department {{ auth()->user()->department_id }}, approval limit {{ auth()->user()->approval_limit }},
            inherits from: {{ $roles[auth()->id()] ?? 'nothing' }}.
            <a href="{{ url('/sign-out') }}">Sign out</a>
        </p>
    @else
        <p>Nobody is signed in: requests are checked as the guest role.</p>
    @endauth

    <h2>Sign in</h2>
    <ul>
        @foreach ($users as $user)
            <li>
                <a href="{{ url('/sign-in/'.$user->getKey()) }}">{{ $user->name }}</a>
                <small style="opacity:.7">{{ $user->email }}: {{ $roles[$user->getKey()] ?? 'holds nothing' }}</small>
                <small><a href="{{ url('/user/'.$user->getKey()) }}">profile</a></small>
            </li>
        @endforeach
        <li><a href="{{ url('/sign-out') }}">Sign out</a></li>
        <li><a href="{{ url('/login') }}">/login</a> <small style="opacity:.7">the page the <code>auth</code> middleware redirects to</small></li>
    </ul>

    @if ($users->isEmpty())
        <p>No users yet. Run <code>php artisan migrate:fresh --seed</code>.</p>
    @endif

    <h2>Examples 1-7: RBAC, <a href="https://github.com/wnikk/laravel-access-rules/blob/main/docs/tutorial-basic-step-by-step.md">first tutorial</a></h2>
    <ol>
        <li><a href="{{ url('/example1') }}">/example1</a> the check in the route, <code>can:example1.viewAny</code></li>
        <li><a href="{{ url('/example2') }}">/example2</a> the check in the action, <code>Gate::authorize('example2.view')</code></li>
        <li>
            <code>/example3/{name|email|password}</code> options of one rule, any verb:
            <a href="{{ url('/example3/name?name=Ann') }}">name</a>,
            <a href="{{ url('/example3/email?email=ann@example.com') }}">email</a>,
            <a href="{{ url('/example3/password?password=secret') }}">password</a>
        </li>
        <li>
            <a href="{{ url('/example4') }}">/example4</a>, <a href="{{ url('/example4/1') }}">/example4/1</a>
            <code>authorizeResource()</code> with global rules; POST, PUT, DELETE with a client
        </li>
        <li>
            <a href="{{ url('/example5') }}">/example5</a>, <a href="{{ url('/example5/1') }}">/example5/1</a>
            the same with rules of the controller, <code>Examples.Example5.*</code>
        </li>
        <li>
            <code>/example6/{news}</code> the suffix <code>.self</code>:
            <a href="{{ url('/example6/1?name=First+news') }}">news 1 (Ann's)</a>,
            <a href="{{ url('/example6/3?name=Not+mine') }}">news 3 (Bob's)</a>
        </li>
        <li>
            <a href="{{ url('/example7') }}">/example7</a>, <a href="{{ url('/example7/1') }}">/example7/1</a>, <a href="{{ url('/example7/2') }}">/example7/2</a>
            a policy in 2.x, a condition in 3.x: news of the last 48 hours
        </li>
    </ol>

    <h2>Examples 8-15: ABAC, <a href="https://github.com/wnikk/laravel-access-rules/blob/main/docs/tutorial-abac-step-by-step.md">second tutorial</a></h2>
    <ol start="8">
        <li>
            <a href="{{ url('/example8') }}">/example8</a> the list;
            <a href="{{ url('/example8/1') }}">/example8/1</a>, <a href="{{ url('/example8/3') }}">/3</a>, <a href="{{ url('/example8/4') }}">/4</a> one record;
            <a href="{{ url('/example8/5/approve') }}">/example8/5/approve</a>, <a href="{{ url('/example8/1/approve') }}">/1/approve</a> the record against the user;
            <a href="{{ url('/example8/1/export/csv') }}">/example8/1/export/csv</a>, <a href="{{ url('/example8/1/export/pdf') }}">/pdf</a>, <a href="{{ url('/example8/4/export/csv') }}">/4/export/csv</a> an option and a condition of the rule
        </li>
        <li>
            <a href="{{ url('/example9') }}">/example9</a> the scenarios of the article on six orders, needs <code>manage-access</code>;
            <a href="{{ url('/example9/reset') }}">/example9/reset</a> back to the seeded state
            <ul>
                @foreach ($scenarios as $name => $what)
                    <li><a href="{{ url('/example9/'.$name) }}">{{ $name }}</a> <small style="opacity:.7">{{ $what }}</small></li>
                @endforeach
            </ul>
        </li>
        <li><a href="{{ url('/example10') }}">/example10</a> a menu, and three ways to ask</li>
        <li>
            <a href="{{ url('/example11') }}">/example11</a>, <a href="{{ url('/example11/1') }}">/example11/1</a>, <a href="{{ url('/example11/3') }}">/example11/3</a>
            guests: open signed out
        </li>
        <li>
            <a href="{{ url('/example12/4') }}">/example12/4</a> a 403 that names what was refused;
            <a href="{{ url('/example12/4?access_debug=1') }}">/example12/4?access_debug=1</a> the same with the cause;
            <a href="{{ url('/example12?access_debug=1') }}">/example12?access_debug=1</a> a list and what narrowed it
        </li>
        <li>
            <a href="{{ url('/example13/explain/2/orders.view/4') }}">/example13/explain/2/orders.view/4</a>,
            <a href="{{ url('/example13/explain/1/orders.view') }}">/example13/explain/1/orders.view</a> explain as data;
            <a href="{{ url('/example13/lint') }}">/example13/lint</a>;
            <a href="{{ url('/example13/audit') }}">/example13/audit</a> the log of the event <code>AccessChanged</code>;
            needs <code>manage-access</code>
        </li>
        <li>
            <a href="{{ url('/example14') }}">/example14</a> XACML: download, check, import;
            <a href="{{ url('/example14/download') }}">/example14/download</a>; needs <code>manage-access</code>
        </li>
        <li>
            <a href="{{ url('/example15') }}">/example15</a> polymorphic relations, example 18 of the article, run as user 3; needs <code>manage-access</code>
            <ul>
                @foreach ($catalog as $name => $what)
                    <li><a href="{{ url('/example15/'.$name) }}">{{ $name }}</a> <small style="opacity:.7">{{ $what }}</small></li>
                @endforeach
            </ul>
        </li>
    </ol>

    <h2>Other</h2>
    <ul>
        <li><a href="{{ url('/user') }}">/user</a>, <a href="{{ url('/user/2') }}">/user/2</a> profile, what the account may do</li>
        <li><a href="{{ url('/access-control') }}">/access-control</a> the panel of wnikk/laravel-access-ui 3.0: rules, owners, permissions with conditions, inheritance, why?, health, XACML; needs <code>manage-access</code></li>
        <li><a href="{{ url('/users') }}">/users</a> every account with a way to its profile and the card, for an administrator</li>
        <li><a href="{{ url('/up') }}">/up</a> health</li>
    </ul>

    <h2>Console</h2>
    <pre>php artisan migrate:fresh --seed
php artisan acr:explain "App\Models\User" 1 orders.view order:4
php artisan acr:lint
php artisan acr:xacml:export storage/app/access.xml
php artisan acr:xacml:import storage/app/access.xml --check
php artisan test</pre>

@endsection
