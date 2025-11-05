@extends('layout.layout1')

@section('title', 'Welcome')

@section('content')
    <div class="text-center">
        <h1>Welcome to {{ config('app.name', 'Warehouse App') }}</h1>
        <p>This is a simple Warehouse App</p>
    </div>
@endsection
