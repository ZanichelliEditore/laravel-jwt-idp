<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Zanichelli</title>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
<div id="content">

    <div id="zanichelli-nav">
        <ul class="nav justify-content-end">
            @if(Route::is('loginForm'))
                {{--<li class="nav-item">--}}
                    {{--<a class="nav-link active" href="{{ route('registerForm') }}">@lang('auth.label-sign-up')</a>--}}
                {{--</li>--}}
            @else
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('loginForm') }}">@lang('auth.label-login')</a>
                </li>
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