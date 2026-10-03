{{--
    The message of a 403 stays the one of Laravel. The package adds two things this page can print:
      Access::lastDenied()  the name of what was refused, always and for free
      Access::debugLog()    the cause, only while debug mode is on (App\Http\Middleware\AccessDebug)
--}}
@extends('layouts.admin', ['title' => '403'])

@php use Wnikk\LaravelAccessRules\Facades\Access; @endphp

@section('content')

    <h1>403</h1>

    <p>{{ $exception->getMessage() ?: 'This action is unauthorized.' }}</p>

    @if (Access::lastDenied())
        <p>Refused: <code>{{ Access::lastDenied() }}</code></p>
    @endif

    @if ($denial = last(Access::debugLog()['denials']))
        <h2>Why</h2>
        <pre style="white-space: pre-wrap">{{ $denial['text'] }}</pre>
    @elseif (app()->environment('local'))
        <p style="opacity:.7">Add <code>?access_debug=1</code> to the address to see the cause.</p>
    @endif

@endsection
