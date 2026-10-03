{{--
    Example 10. A menu item has no record yet, so it asks with the class of the model:
    "is there any order this user may see?". Sign in as Ann, as Bob and as user 3 to compare.
--}}
@extends('layouts.admin', ['title' => 'Example 10: menu'])

@section('content')

    <h1>Menu</h1>

    <ul>
        @can('orders.view', App\Models\Order::class)
            <li><a href="{{ url('/example8') }}">Orders</a></li>
        @endcan

        @can('example1.viewAny')
            <li><a href="{{ url('/example1') }}">Users</a></li>
        @endcan

        @can('manage-access')
            <li><a href="{{ url('/example14') }}">Access control: XACML</a></li>
        @endcan

        <li><a href="{{ url('/example11') }}">Catalogue</a> <small style="opacity:.7">(no check: the list filters itself)</small></li>
    </ul>

    <h2>Three ways to ask</h2>

    @auth
        <table cellpadding="6">
            @foreach ($answers as $question => $answer)
                <tr>
                    <td><code>{{ $question }}</code></td>
                    <td><strong>{{ $answer ? 'true' : 'false' }}</strong></td>
                </tr>
            @endforeach
        </table>
    @else
        <p>Sign in to see the answers: <a href="{{ url('/sign-in/1') }}">Ann</a>, <a href="{{ url('/sign-in/2') }}">Bob</a>, <a href="{{ url('/sign-in/3') }}">user 3</a>.</p>
    @endauth

@endsection
