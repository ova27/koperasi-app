<x-app-layout>
    <div class="max-w-4xl mx-auto">

        <h1 class="text-xl font-semibold mb-4">
            Proses Keluar Anggota
        </h1>

        <div class="bg-white border rounded p-4 mb-6">
            <p><strong>Nama:</strong> {{ $anggota->nama }}</p>
            <p><strong>Status:</strong> {{ $anggota->status }}</p>
        </div>

        <div class="bg-yellow-50 border border-yellow-300 rounded p-4 mb-6">
            <h2 class="font-semibold mb-2">Saldo Simpanan</h2>

            <ul class="text-sm">
                @foreach (['pokok', 'wajib', 'sukarela'] as $jenis)
                    <li>
                        {{ ucfirst($jenis) }}:
                        Rp {{ number_format($saldos[$jenis]->total ?? 0) }}
                    </li>
                @endforeach
            </ul>
        </div>

        @if ($errors->any())
            <div class="text-red-600 mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.anggota.keluar.process', $anggota) }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium">Alasan</label>
                <select name="alasan"
                        class="border rounded w-full px-2 py-1"
                        required>
                    <option value="">-- pilih --</option>
                    <option value="pensiun">Pensiun</option>
                    <option value="mutasi">Mutasi</option>
                </select>
            </div>

            <button class="bg-red-600 text-white px-4 py-2 rounded">
                Proses Keluar Anggota
            </button>
        </form>

    </div>
</x-app-layout>
