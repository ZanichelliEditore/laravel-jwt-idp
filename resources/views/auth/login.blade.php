@extends('base')

@section('content')

    <login-form redirect={{request('redirect')}}></login-form>

@endsection