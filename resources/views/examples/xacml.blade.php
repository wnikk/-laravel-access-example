{{--
    Example 14. Download the export, then upload it back to see the plan of an import.
    Change a condition in between (/example9/priority) and the plan shows what differs.
--}}
@extends('layouts.admin', ['title' => 'Example 14: XACML'])

@section('content')

    <h1>XACML 3.0</h1>

    <p><a href="{{ url('/example14/download') }}">Download access-rules.xml</a> (one XACML 3.0 document; titles, names and inheritance travel inside it)</p>

    <form method="post" action="{{ url('/example14/check') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="policy" required>
        <button>Check: what would an import change?</button>
        <button formaction="{{ url('/example14/import') }}">Import what is missing</button>
    </form>

    @error('policy') <p style="color:#c00">{{ $message }}</p> @enderror

    @if ($report)
        <h2>Plan, nothing was written</h2>

        @foreach (['errors', 'warnings'] as $kind)
            @foreach ($report[$kind] as [$at, $text])
                <p><strong>{{ $kind }}</strong> <code>{{ $at }}</code> {{ $text }}</p>
            @endforeach
        @endforeach

        <table cellpadding="6" border="1" style="border-collapse:collapse">
            <tr><th>Kind</th><th>Action</th><th>What</th><th>Document</th><th>Database</th></tr>
            @foreach ($report['changes'] as $change)
                @continue($change['action'] === 'same')
                <tr>
                    <td>{{ $change['kind'] }}</td>
                    <td>{{ $change['action'] }}</td>
                    <td>{{ $change['what'] }}</td>
                    <td><code>{{ $change['document'] }}</code></td>
                    <td><code>{{ $change['database'] }}</code></td>
                </tr>
            @endforeach
        </table>

        <pre>{{ json_encode($report['summary'], JSON_PRETTY_PRINT) }}</pre>
    @endif

@endsection
