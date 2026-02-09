@extends('layouts.main')
@section('title', 'Generate Simpanan Wajib')
@section('content')
<div class="max-w-xl mx-auto">

    <h1 class="text-xl font-semibold mb-4">
        Generate Simpanan Wajib
    </h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            {{ $errors->first() }}
        </div>
    @endif


    <form method="POST" action="{{ route('admin.simpanan.generate-wajib.process') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm mb-1">
                Bulan Simpanan
            </label>
            <input
                type="month"
                name="bulan"
                class="border rounded w-full px-3 py-2"
                required
            >
        </div>

        <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded"
        >
            Generate Simpanan Wajib
        </button>
    </form>

</div>
@endsection
