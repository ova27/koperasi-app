@extends('layouts.main')

@section('title', 'Simpanan Saya')
@section('page-title', 'Simpanan Saya')

@section('content')
<div class="space-y-10">

    {{-- SALDO --}}
    <div>
        <h2 class="section-title">Saldo Simpanan Saya</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            @foreach($saldo as $jenis => $jumlah)
                <div class="stat-card stat-info">
                    <div class="stat-label">
                        {{ ucfirst($jenis) }}
                    </div>
                    <div class="stat-value">
                        Rp {{ number_format($jumlah, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- RIWAYAT SIMPANAN --}}
    <div>
        <h2 class="section-title">
            Riwayat Simpanan
        </h2>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left w-[18%]">
                            Tanggal
                        </th>
                        <th class="px-4 py-3 text-center w-[18%]">
                            Jenis
                        </th>
                        <th class="px-4 py-3 text-right w-[20%]">
                            Jumlah
                        </th>
                        <th class="px-4 py-3 text-center w-[44%]">
                            Keterangan
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($simpanan as $s)
                        <tr class="border-t hover:bg-gray-50">

                            {{-- TANGGAL --}}
                            <td class="px-4 py-3 text-gray-600 w-[18%]">
                                {{ $s->tanggal->format('d M Y') }}
                            </td>

                            {{-- JENIS --}}
                            <td class="px-4 py-3 text-center w-[18%]">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($s->jenis_simpanan === 'pokok') bg-blue-100 text-blue-800
                                    @elseif($s->jenis_simpanan === 'wajib') bg-indigo-100 text-indigo-800
                                    @elseif($s->jenis_simpanan === 'sukarela') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-700
                                    @endif
                                ">
                                    {{ ucfirst($s->jenis_simpanan) }}
                                </span>
                            </td>

                            {{-- JUMLAH --}}
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 w-[20%]">
                                Rp {{ number_format($s->jumlah, 0, ',', '.') }}
                            </td>

                            {{-- KETERANGAN --}}
                            <td class="px-4 py-3 text-center text-gray-600 w-[44%]">
                                {{ $s->keterangan ?? '-' }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                Belum ada riwayat simpanan.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

            </table>
        </div>
    </div>

</div>
@endsection