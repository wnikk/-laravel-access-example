<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rules and Roles example</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Ubuntu:regular,bold"/>
    <style>
        body {font-family: Ubuntu, roman, serif;}
    </style>

    @include('accessUi::links')

    <script>
        document.addEventListener("DOMContentLoaded", function(e) {
            accessUi.init(document.getElementById('container-roles'), 'rules', {
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
                availableRules: true,
                availableOwners: true,
                availableInherit: true,
                availablePermission: true,
                routeRules: {
                    list:   '{{ route('userRoles.rule', 'index') }}',
                    create: '{{ route('userRoles.rule', 'store') }}',
                    update: '{{ route('userRoles.rule', ['update', ':id:']) }}',
                    delete: '{{ route('userRoles.rule', ['destroy', ':id:']) }}',
                },
                routeOwners: {
                    list:   '{{ route('userRoles.role', 'index') }}',
                    create: '{{ route('userRoles.role', 'store') }}',
                    update: '{{ route('userRoles.role', ['update', ':id:']) }}',
                    delete: '{{ route('userRoles.role', ['destroy', ':id:']) }}',
                },
                routeInherit: {
                    list:   '{{ route('userRoles.inherit', ['index', ':owner:']) }}',
                    create: '{{ route('userRoles.inherit', ['store', ':owner:']) }}',
                    delete: '{{ route('userRoles.inherit', ['destroy', ':owner:', ':id:']) }}',
                },
                routePermission: {
                    list:   '{{ route('userRoles.permission', ['index', ':owner:']) }}',
                    update: '{{ route('userRoles.permission', ['update', ':owner:', ':id:']) }}',
                },
            });
        });
    </script>

</head>
<body>

    <div class="container mt-3">
        <h1>Control rules and roles example</h1>

        <div id="container-roles"></div>
    </div>

</body>
</html>
