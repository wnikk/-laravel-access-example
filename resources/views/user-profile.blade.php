<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ 'User #'.$user->id.' '.$user->name }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Ubuntu:regular,bold"/>
    <style>
        body {font-family: Ubuntu, roman, serif;}
    </style>

    @include('accessUi::links')

    <script>
        document.addEventListener("DOMContentLoaded", function(e)
        {
            accessUi.init(document.getElementById('container-inherit'), 'inherit',{
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
                routeInherit: {
                    list:   '{{ route('userRoles.inherit', ['index', $owner_id]) }}',
                    create: '{{ route('userRoles.inherit', ['store', $owner_id]) }}',
                    delete: '{{ route('userRoles.inherit', ['destroy', $owner_id, ':id:']) }}',
                },
            });
            accessUi.init(document.getElementById('container-permission'), 'permission',{
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
                routePermission: {
                    list:   '{{ route('userRoles.permission', ['index', $owner_id]) }}',
                    update: '{{ route('userRoles.permission', ['update', $owner_id, ':id:']) }}',
                },
            });
        });
    </script>

</head>
<body>

    <div class="container">

        <h1>{{ $user->name }} (ID: {{ $user->id }})</h1>
        <p>
            <label>email:</label>
            <strong>{{ $user->email }}</strong>
        </p>

        <div>

            <h4 class="mt-3">inherit</h4>
            <div id="container-inherit"></div>

            <h4 class="mt-3">permission</h4>
            <div id="container-permission"></div>

        </div>
    </div>

</body>
</html>
