@props(['status'])

@php
$map = [
    'aktif' => 'bg-green-100 text-green-700',
    'cuti' => 'bg-yellow-100 text-yellow-700',
    'tugas_belajar' => 'bg-blue-100 text-blue-700',
    'tidak_aktif' => 'bg-red-100 text-red-700',
];
@endphp

<span class="px-2 py-1 rounded text-xs font-semibold
    {{ $map[$status] ?? 'bg-gray-100 text-gray-600' }}">
    {{ ucfirst(str_replace('_',' ', $status)) }}
</span>
