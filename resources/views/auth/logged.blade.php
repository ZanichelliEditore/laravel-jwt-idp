@extends('base')

@section('content')
    <h4 class="text-center mt-5">{{ session()->get('user')->name }} sei già loggato</h4>
@endsection