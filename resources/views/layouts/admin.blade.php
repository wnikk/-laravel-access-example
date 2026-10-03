{{--
    Host layout for the access-ui panel.

    config/accessUi.php names this view under `layout.view` and `content` under
    `layout.section`, so the package extends it and pushes its markup into that
    section. The panel brings its own CSS and JS — the only obligations here are
    to yield the section and to carry a csrf-token meta tag.

    Set `layout.view` back to null to see the package's built-in standalone page
    instead of this one.
--}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    {{--
        Messages of the panel in the language of the session, registered before the bundle loads:
        the bundle reads window.accessUiMessages once, keyed by locale. English lives in the
        package; this file is the application's own.
    --}}
    @if (app()->getLocale() !== 'en' && is_file(lang_path('vendor/accessUi/'.app()->getLocale().'/ui.json')))
        <script>window.accessUiMessages = { {{ app()->getLocale() }}: {!! file_get_contents(lang_path('vendor/accessUi/'.app()->getLocale().'/ui.json')) !!} };</script>
    @endif
    <style>
        :root { color-scheme: light dark; }
        body {
            margin: 0;
            font: 15px/1.5 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .app-bar {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            padding: .75rem 1.25rem;
            border-bottom: 1px solid rgba(127, 127, 127, .35);
        }
        .app-bar strong { margin-right: auto; }
        .app-bar a { color: inherit; text-decoration: none; opacity: .75; }
        .app-bar a:hover,
        .app-bar a:focus { opacity: 1; text-decoration: underline; }
        .app-main { padding: 1.25rem; }
    </style>
</head>
<body>

<nav class="app-bar">
    <strong>{{ $title ?? config('app.name') }}</strong>

    <a href="{{ url('/') }}">Examples</a>

    @auth
        <span>{{ auth()->user()->name }}</span>
        <a href="{{ url('/user') }}">Profile</a>
        @can('manage-access')
            <a href="{{ url('/users') }}">Users</a>
            {{-- The panel of wnikk/laravel-access-ui; the link exists only while its routes are registered --}}
            @if (Route::has('accessUi.index'))
                <a href="{{ url(config('accessUi.routes.prefix')) }}">{{ __('Access control') }}</a>
            @endif
        @endcan
        <a href="{{ url('/sign-out') }}">Sign out</a>
    @endauth

    <span style="opacity:.6">
        @foreach (\App\Http\Middleware\SetLocale::LOCALES as $locale)
            <a href="{{ url('/lang/'.$locale) }}" @if (app()->getLocale() === $locale) style="opacity:1;font-weight:600" @endif>{{ strtoupper($locale) }}</a>
        @endforeach
    </span>

    @guest
        <a href="{{ url('/sign-in/1') }}">Ann</a>
        <a href="{{ url('/sign-in/2') }}">Bob</a>
        <a href="{{ url('/sign-in/3') }}">user 3</a>
    @endguest
</nav>

<main class="app-main">
    @if (session('status'))
        <p style="opacity:.7">{{ session('status') }}</p>
    @endif

    @yield('content')
</main>

</body>
</html>
