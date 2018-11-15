@extends('base')

@section('content')

    <div class="col-10 col-sm-8 col-md-6 col-lg-4 mr-auto ml-auto border px-3 py-4 mt-3 mb-5">
        <form method="post" action="{{ route('register') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="inputEmail">@lang('auth.label-email')</label>
                <input id="inputEmail" type="email" class="form-control"  name="email"
                       placeholder="@lang('auth.label-enter-email')" required="required">
            </div>
            <div class="form-group">
                <label for="inputPassword">Password</label>
                <input id="inputPassword" type="password" class="form-control" name="password"
                       placeholder="@lang('auth.label-enter-password')" required="required">
            </div>
            <div class="form-group">
                <label for="inputPassword">@lang('auth.label-confirm-password')</label>
                <input id="inputPassword" type="password" class="form-control" name="password_confirmation"
                       placeholder="@lang('auth.label-repeat-password')" required="required">
            </div>
            <div class="form-group">
                <label for="inputName">@lang('auth.label-name')</label>
                <input id="inputName" type="text" class="form-control" name="name"
                       placeholder="@lang('auth.label-enter-name')" required="required">
            </div>
            <div class="form-group">
                <label for="inputSurname">@lang('auth.label-surname')</label>
                <input id="inputSurname" type="text" class="form-control" name="surname"
                       placeholder="@lang('auth.label-enter-surname')" required="required">
            </div>
            <button type="submit" class="d-block col-4 btn btn-primary ml-auto mr-auto">
                @lang('auth.label-sign-up')
            </button>
        </form>
    </div>

@endsection