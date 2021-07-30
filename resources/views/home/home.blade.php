@extends('layout')

@section('content')
    <h1>Hello {{ auth()->check() ? auth()->user()->nice_name : 'guest' }}</h1>

@endsection