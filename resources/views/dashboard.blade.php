@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

    <p>Selamat datang, {{ auth()->user()->name }}.</p>

    <p class="mt-2 text-sm text-gray-600">
        Role: {{ auth()->user()->getRoleNames()->implode(', ') }}
    </p>
@endsection
