<form id="form-edit-anggota"
      method="POST"
      action="{{ route('admin.anggota.update', $anggota) }}"
      class="space-y-0">

    @csrf
    @method('PUT')

    @can('edit anggota')
        {{-- HEADER --}}
        <div class="flex items-start justify-between px-6 pt-6 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-4 min-w-0">
                <div class="flex-shrink-0 inline-flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-blue-600 text-base font-bold text-white shadow-md">
                    {{ strtoupper(substr($anggota->nama, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h2 id="modal-title" class="text-lg font-bold text-slate-800 truncate">Edit Data Anggota</h2>
                    <p class="text-sm text-slate-500 truncate">{{ $anggota->nama }}</p>
                </div>
            </div>
            <button type="button"
                    onclick="closeModal()"
                    class="flex-shrink-0 ml-4 inline-flex items-center justify-center h-8 w-8 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- BODY --}}
        <div class="px-6 py-5 space-y-4 max-h-[65vh] overflow-y-auto">
            <div id="form-error" class="hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nama</label>
                    <input disabled
                        value="{{ $anggota->nama }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                    <span class="mt-1 block text-xs text-red-500 error-nama"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email</label>
                    <input disabled
                        value="{{ $anggota->user->email ?? '-' }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">NIP</label>
                    <input type="text"
                        name="nip"
                        value="{{ old('nip', $anggota->nip) }}"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                    <span class="mt-1 block text-xs text-red-500 error-nip"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Jenis Kelamin</label>
                    <select name="jenis_kelamin"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
                            required>
                        <option value="L" @selected($anggota->jenis_kelamin === 'L')>Laki-laki</option>
                        <option value="P" @selected($anggota->jenis_kelamin === 'P')>Perempuan</option>
                    </select>
                    <span class="mt-1 block text-xs text-red-500 error-jenis_kelamin"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Jabatan</label>
                    <input type="text"
                        name="jabatan"
                        value="{{ old('jabatan', $anggota->jabatan) }}"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                    <span class="mt-1 block text-xs text-red-500 error-jabatan"></span>
                </div>

                @can('nonaktifkan anggota')
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status Anggota</label>
                        <select name="status"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                required>
                            <option value="aktif" @selected($anggota->status === 'aktif')>Aktif</option>
                            <option value="cuti" @selected($anggota->status === 'cuti')>Cuti</option>
                            <option value="tugas_belajar" @selected($anggota->status === 'tugas_belajar')>Tugas Belajar</option>
                            <option disabled value="tidak_aktif" @selected($anggota->status === 'tidak_aktif')>Pensiun / Mutasi</option>
                        </select>
                        <span class="mt-1 block text-xs text-red-500 error-status"></span>
                    </div>
                @endcan
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-100 rounded-b-2xl">
            <button type="button"
                    onclick="closeModal()"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400">
                Batal
            </button>
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-400">
                Simpan Perubahan
            </button>
        </div>
    @endcan
</form>

<script>
    if (!window.__formEditAnggotaSubmitBound) {
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.id !== 'form-edit-anggota') return;

            e.preventDefault();

            form.querySelectorAll('[class^="error-"]').forEach(el => el.textContent = '');

            const formError = document.getElementById('form-error');
            if (formError) {
                formError.classList.add('hidden');
                formError.textContent = '';
            }

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

                    Object.keys(data.errors).forEach(key => {
                        const el = form.querySelector('.error-' + key);
                        if (el) el.textContent = data.errors[key][0];
                    });

                    return null;
                }

                return res.json();
            })
            .then(res => {
                if (res && res.success) {
                    closeModal();
                    location.reload();
                }
            })
            .catch(() => {
                if (formError) {
                    formError.textContent = 'Gagal menyimpan perubahan. Silakan coba lagi.';
                    formError.classList.remove('hidden');
                }
            });
        });

        window.__formEditAnggotaSubmitBound = true;
    }
</script>
