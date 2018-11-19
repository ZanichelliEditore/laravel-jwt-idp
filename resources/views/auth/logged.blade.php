@extends('base')

@section('content')
    <h4 class="text-center mt-5">{{ session()->get('user')->name }} @lang('auth.label-logged')</h4>
@endsection