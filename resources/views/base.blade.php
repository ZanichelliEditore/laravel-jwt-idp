<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel JWT IDP</title>

    <link rel="stylesheet" href="css/app.css">
    <script src="js/app.js"></script>

    <link rel="stylesheet" href="css/style.css">

</head>
<body>
<div id="content">

    <div id="zanichelli-nav">
        <ul class="nav justify-content-end">
            @if(session('user'))
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('logout', ['token' => session()->get('token')]) }}">@lang('auth.label-logout')</a>
                </li>
            @else
                @if(Route::is('loginForm'))
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('registerForm') }}">@lang('auth.label-sign-up')</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('loginForm') }}">@lang('auth.label-login')</a>
                    </li>
                @endif
            @endif

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">{{ strtoupper(App::getLocale('locale')) }}</a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="locale/it">IT</a>
                    <a class="dropdown-item" href="locale/en">EN</a>
                </div>
            </li>
        </ul>
    </div>

    @yield('content')

</div>
</body>
</html>