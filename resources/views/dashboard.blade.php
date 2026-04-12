@extends('layouts.main')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-3">
    
    {{-- ========================= --}}
    {{-- RINGKASAN KOPERASI --}}
    {{-- ========================= --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <h2 class="section-title mb-6">
            Ringkasan Koperasi
        </h2>

        {{-- ========================= --}}
        {{-- RINGKASAN UTAMA --}}
        {{-- ========================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-2">

            {{-- ANGGOTA --}}
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl px-4 py-3 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-blue-200 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-6 w-6 text-blue-600"
                             fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 19.128a9.38 9.38 0 002.625.372
                                     9.337 9.337 0 004.121-.952
                                     4.125 4.125 0 00-7.533-2.493
                                     M9 5.25a3 3 0 116 0 3 3 0 01-6 0z" />
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-gray-600">Anggota Aktif</div>
                </div>
                <div class="mx-3 text-3xl font-bold text-gray-900">
                    {{ $anggotaAktif ?? 0 }}
                </div>
            </div>

            {{-- TOTAL SIMPANAN --}}
            <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl px-4 py-3 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-green-200 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-6 w-6 text-green-600"
                             fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 10.5h18m-18 0A2.25 2.25 0 015.25 8.25
                                     h13.5A2.25 2.25 0 0121 10.5m-18 0v3
                                     A2.25 2.25 0 005.25 15.75h13.5
                                     A2.25 2.25 0 0021 13.5v-3
                                     m-9 1.5h.008v.008H12V12z" />
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-gray-600">Total Simpanan</div>
                </div>
                <div class="mx-2 text-2xl font-bold text-gray-900">
                    Rp {{ number_format($totalSimpanan ?? 0, 0, ',', '.') }}
                </div>
            </div>

            {{-- TOTAL PINJAMAN --}}
            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl px-4 py-3 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-yellow-200 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-6 w-6 text-yellow-600"
                             fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.25 8.25h19.5M4.5 6h15
                                     a2.25 2.25 0 012.25 2.25v7.5
                                     A2.25 2.25 0 0119.5 18h-15
                                     a2.25 2.25 0 01-2.25-2.25v-7.5
                                     A2.25 2.25 0 014.5 6z" />
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-gray-600">Total Pinjaman</div>
                </div>
                <div class="mx-2 text-2xl font-bold text-gray-900">
                    Rp {{ number_format($totalPinjaman ?? 0, 0, ',', '.') }}
                </div>
            </div>

        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
        {{-- DETAIL RINGKASAN (KIRI) --}}
        <div class="bg-white border border-gray-200 rounded-xl px-6 pt-6 pb-6 shadow-sm">
            <h2 class="section-title mb-4">
                Detail Ringkasan
                <span class="text-base text-gray-600 font-normal">
                    ({{ $bulan }} {{ $tahun }})
                </span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                {{-- SIMPANAN --}}
                <div class="space-y-2">
                    <h3 class="text-sm mx-1 font-semibold text-gray-700">Simpanan</h3>
                    <div class="grid grid-cols-1 gap-2">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="text-sm text-green-800 font-medium">Simpanan Pokok</div>
                            <div class="text-lg font-bold text-green-900">
                                Rp {{ number_format($simpananPokok ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="text-sm text-green-800 font-medium">Simpanan Wajib</div>
                            <div class="text-lg font-bold text-green-900">
                                Rp {{ number_format($simpananWajib ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="text-sm text-green-800 font-medium">Simpanan Sukarela</div>
                            <div class="text-lg font-bold text-green-900">
                                Rp {{ number_format($simpananSukarela ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PINJAMAN --}}
                <div class="space-y-2">
                    <h3 class="text-sm mx-1 font-semibold text-gray-700">Pinjaman</h3>
                    <div class="grid grid-cols-1 gap-2">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="text-sm text-yellow-800 font-medium">Pinjaman Aktif</div>
                            <div class="text-lg font-bold text-yellow-900">
                                {{ $pinjamanAktif ?? 0 }}
                            </div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="text-sm text-yellow-800 font-medium">Antrian Pinjaman</div>
                            <div class="text-lg font-bold text-yellow-900">
                                {{ $antrianPinjaman ?? 0 }}
                            </div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="text-sm text-yellow-800 font-medium">Sisa Pinjaman Aktif</div>
                            <div class="text-lg font-bold text-yellow-900">
                                Rp {{ number_format($sisaPinjamanAktif ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRAFIK TREN (KANAN) --}}
        <div class="bg-white border border-gray-200 rounded-xl px-6 pt-6 pb-3 shadow-sm flex flex-col">
            <h2 class="section-title">
                Tren Bulanan
            </h2>

            <div class="flex-1 mt-4">
                <div class="w-full h-[280px]">
                    <canvas id="trenChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- LAST UPDATED --}}
    {{-- ========================= --}}
    <div class="text-right text-xs text-gray-400">
        Terakhir diperbarui:
        @if($lastUpdated)
            {{ $lastUpdated->format('d M Y H:i') }}
        @else
            -
        @endif
    </div>

</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Chart.js loaded:', typeof Chart);
        const ctx = document.getElementById('trenChart');
        if (!ctx) {
            console.error('Canvas element not found');
            return;
        }
        const chartData = @json($chartData);
        console.log('Chart data:', chartData);

        if (!chartData || chartData.length === 0) {
            console.warn('No chart data available');
            const context = ctx.getContext('2d');
            context.font = '16px Arial';
            context.fillText('No data available', 10, 50);
            return;
        }

        const labels = chartData.map(item => item.month);
        const simpananData = chartData.map(item => item.simpanan);
        const pinjamanData = chartData.map(item => item.pinjaman);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Simpanan',
                    data: simpananData,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.1
                }, {
                    label: 'Pinjaman',
                    data: pinjamanData,
                    borderColor: 'rgb(245, 158, 11)',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
