<form id="form-edit-anggota"
      method="POST"
      action="{{ route('admin.anggota.update', $anggota) }}"
      class="space-y-4">

    @csrf
    @method('PUT')

    {{-- NAMA (READ ONLY) --}}
    <div>
        <label class="text-sm text-gray-600">Nama</label>
        <input disabled
               value="{{ $anggota->nama }}"
               class="w-full border rounded px-3 py-2 bg-gray-100">
    </div>

    {{-- NIP --}}
    <div>
        <label class="text-sm text-gray-600">NIP</label>
        <input type="text"
               name="nip"
               value="{{ old('nip', $anggota->nip) }}"
               class="w-full border rounded px-3 py-2">
    </div>

    {{-- JENIS KELAMIN --}}
    <div>
        <label class="text-sm text-gray-600">Jenis Kelamin</label>
        <select name="jenis_kelamin"
                class="w-full border rounded px-3 py-2"
                required>
            <option value="L" @selected($anggota->jenis_kelamin === 'L')>
                Laki-laki
            </option>
            <option value="P" @selected($anggota->jenis_kelamin === 'P')>
                Perempuan
            </option>
        </select>
    </div>

    {{-- JABATAN --}}
    <div>
        <label class="text-sm text-gray-600">Jabatan</label>
        <input type="text"
               name="jabatan"
               value="{{ old('jabatan', $anggota->jabatan) }}"
               class="w-full border rounded px-3 py-2">
    </div>

    {{-- STATUS --}}
    @can('nonaktifkan anggota')
        <div>
            <label class="text-sm text-gray-600">Status Anggota</label>
            <select name="status"
                    class="w-full border rounded px-3 py-2"
                    required>
                <option value="aktif" @selected($anggota->status === 'aktif')>
                    Aktif
                </option>
                <option value="cuti" @selected($anggota->status === 'cuti')>
                    Cuti
                </option>
                <option value="tugas_belajar" @selected($anggota->status === 'tugas_belajar')>
                    Tugas Belajar
                </option>
                <option value="tidak_aktif" @selected($anggota->status === 'tidak_aktif')>
                    Pensiun / Mutasi
                </option>
            </select>
        </div>
    @endcan

    {{-- FOOTER --}}
    <div class="flex justify-between items-center pt-4 border-t">
        <button class="px-4 py-2 bg-blue-600 text-white rounded">
            Simpan Perubahan
        </button>
    </div>

</form>
