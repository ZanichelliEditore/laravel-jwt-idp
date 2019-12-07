<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Zanichelli</title>

    <link rel="icon" type="image/png" href="/images/favicon.png" />

    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/style.css">

    <script defer src="https://use.fontawesome.com/releases/v5.7.2/js/all.js"
            integrity="sha384-0pzryjIRos8mFBWMzSSZApWtPl/5++eIfzYmTgBBmXYdhvxPc+XcFEk+zJwDgWbP" crossorigin="anonymous">
    </script>

</head>
<body>
    <div id="app">
        <div id="zanichelli-nav">
            <ul class="nav justify-content-end">
                @auth
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('logout') }}">@lang('auth.label-logout')</a>
                    </li>
                @elseauth
                    @if(Route::is('loginForm'))
                    @else
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('loginForm') }}">@lang('auth.label-login')</a>
                        </li>
                    @endif
                @endauth
            </ul>
        </div>

        @yield('content')
    </div>
    
    <script src="{{mix('js/app.js')}}"></script>
</body>
</html>