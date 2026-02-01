<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">
                {{ $anggota->nama }}
            </h2>
            <div class="mt-1">
                <x-status-anggota :status="$anggota->status" /> <br>
            </div>
        </div>
    </div>

    {{-- PROFIL SINGKAT --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div>
            <div class="text-gray-500">Email</div>
            <div class="font-medium">
                {{ $anggota->user->email ?? '-' }}
            </div>
        </div>

        <div>
            <div class="text-gray-500">Tanggal Masuk</div>
            <div class="font-medium">
                {{ \Carbon\Carbon::parse($anggota->tanggal_masuk)->format('d-m-Y') }}
            </div>
        </div>
    </div>

    {{-- RINGKASAN SIMPANAN --}}
    <div class="border rounded-lg p-4">
        <h3 class="font-semibold mb-3 text-sm">
            Ringkasan Simpanan
        </h3>

        <table class="w-full text-sm">
            <tr>
                <td class="text-gray-600">Pokok</td>
                <td class="text-right">
                    Rp {{ number_format($pokok, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td class="text-gray-600">Wajib</td>
                <td class="text-right">
                    Rp {{ number_format($wajib, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td class="text-gray-600">Sukarela</td>
                <td class="text-right">
                    Rp {{ number_format($sukarela, 0, ',', '.') }}
                </td>
            </tr>
            <tr class="font-semibold border-t">
                <td>Total</td>
                <td class="text-right">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- RINGKASAN PINJAMAN --}}
    <div class="border rounded-lg p-4">
        <h3 class="font-semibold mb-3 text-sm">
            Ringkasan Pinjaman
        </h3>

        <table class="w-full text-sm">
            <tr>
                <td class="text-gray-600">Pinjaman Aktif</td>
                <td class="text-right">
                    {{ $ringkasanPinjaman['aktif'] }}
                </td>
            </tr>
            <tr>
                <td class="text-gray-600">Pinjaman Lunas</td>
                <td class="text-right">
                    {{ $ringkasanPinjaman['lunas'] }}
                </td>
            </tr>
            <tr class="font-semibold border-t">
                <td>Sisa Pinjaman</td>
                <td class="text-right">
                    Rp {{ number_format($ringkasanPinjaman['sisa'], 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- FOOTER AKSI --}}
    <div class="flex justify-end gap-3 pt-4 border-t text-sm">

        {{-- LINK KE DETAIL LENGKAP --}}
        <a href="{{ route('admin.anggota.show', $anggota) }}"
           class="text-blue-600 hover:text-blue-800 hover:underline">
            Lihat Detail Lengkap
        </a>

    </div>

</div>
