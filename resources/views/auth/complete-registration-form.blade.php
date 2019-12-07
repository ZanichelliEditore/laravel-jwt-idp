@extends('base')

@section('content')

    <complete-registration-form token="{{ request('token') }}"></complete-registration-form>

@endsection