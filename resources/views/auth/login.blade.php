@extends('base')

@section('content')

    <div id="login-form-panel2" class="col-10 col-sm-8 col-md-6 col-lg-4 mr-auto ml-auto border px-3 py-4 mt-5">
        <form method="post" action="{{ route('login') }}">
            <input type="hidden" name="redirect" value="{{app('request')->input('redirect')}}">
            <div class="form-group">
                <label for="inputUsername">@lang('auth.label-email')</label>
                <input id="inputUsername" type="text" class="form-control"  name="email"
                       placeholder="@lang('auth.label-enter-email')" required="required">
            </div>
            <div class="form-group">
                <label for="inputPassword">Password</label>
                <input id="inputPassword" type="password" class="form-control" name="password"
                       placeholder="Password" required="required">
            </div>
            <button id="button-submit-login" type="submit" class="btn btn-primary">@lang('auth.label-login')</button>
        </form>
    </div>

    @if ($errors->any())
        <div id="panel-errors-login" class="alert alert-danger col-10 col-sm-8 col-md-6 col-lg-4 mr-auto ml-auto px-3 py-4 mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

@endsection