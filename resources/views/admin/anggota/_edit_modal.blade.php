{{-- GLOBAL ERROR --}}
<div id="form-error" class="hidden mb-3 text-sm text-red-600"></div>

<form id="form-edit-anggota"
      method="POST"
      action="{{ route('admin.anggota.update', $anggota) }}"
      class="space-y-4">

    @csrf
    @method('PUT')

    @can('edit anggota')
        {{-- NAMA (READ ONLY) --}}
        <div>
            <label class="text-sm text-gray-600">Nama</label>
            <input disabled
                value="{{ $anggota->nama }}"
                class="w-full border rounded px-3 py-2 bg-gray-100">
            <span class="text-xs text-red-500 error-nama"></span>
        </div>

        <div>
            <label class="block text-sm text-gray-600">Email</label>
            <input disabled
                    value="{{ $anggota->user->email ?? '-' }}"
                    class="w-full border rounded px-3 py-2 bg-gray-100">
        </div>

        {{-- NIP --}}
        <div>
            <label class="text-sm text-gray-600">NIP</label>
            <input type="text"
                name="nip"
                value="{{ old('nip', $anggota->nip) }}"
                class="w-full border rounded px-3 py-2">
            <span class="text-xs text-red-500 error-nip"></span>
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
            <span class="text-xs text-red-500 error-jenis_kelamin"></span>
        </div>

        {{-- JABATAN --}}
        <div>
            <label class="text-sm text-gray-600">Jabatan</label>
            <input type="text"
                name="jabatan"
                value="{{ old('jabatan', $anggota->jabatan) }}"
                class="w-full border rounded px-3 py-2">
            <span class="text-xs text-red-500 error-jabatan"></span>
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
                <span class="text-xs text-red-500 error-status"></span>
            </div>
        @endcan

        {{-- FOOTER AKSI --}}
        <div class="flex justify-between items-center pt-4 border-t gap-3">
            <button type="button"
                    onclick="closeModal()"
                    class="flex-1 px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium">
                Batal
            </button>
            <button type="submit"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                Simpan Perubahan
            </button>

        </div>
        
    @endcan
</form>

<script>
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.id !== 'form-edit-anggota') return;

        e.preventDefault();

        // reset error
        form.querySelectorAll('[class^="error-"]').forEach(el => el.textContent = '');
        document.getElementById('form-error').classList.add('hidden');

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async res => {
            if (res.status === 422) {
                const data = await res.json();

                // tampilkan error per field
                Object.keys(data.errors).forEach(key => {
                    const el = form.querySelector('.error-' + key);
                    if (el) el.textContent = data.errors[key][0];
                });

                return;
            }

            return res.json();
        })
        .then(res => {
            if (res && res.success) {
                closeModal();
                location.reload();
            }
        });
    });
</script>
