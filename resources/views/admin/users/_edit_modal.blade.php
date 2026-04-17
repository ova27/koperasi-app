<form id="form-edit-user"
      method="POST"
      action="{{ route('admin.users.update', $user) }}"
      class="space-y-0">

    @csrf
    @method('PUT')

    @php
        $userRoles = $user->roles->pluck('name')->toArray();
        $availableRoles = ['admin', 'ketua', 'bendahara', 'anggota'];
        $roleToneMap = [
            'admin' => 'border-red-200 bg-red-50 text-red-700',
            'ketua' => 'border-violet-200 bg-violet-50 text-violet-700',
            'bendahara' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            'anggota' => 'border-blue-200 bg-blue-50 text-blue-700',
        ];
    @endphp

    <div class="flex items-start justify-between border-b border-slate-100 px-6 pb-4 pt-6">
        <div class="flex min-w-0 items-center gap-4">
            <div class="inline-flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-blue-600 text-base font-bold text-white shadow-md">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <h2 id="modal-title" class="truncate text-lg font-bold text-slate-800">Edit Akun Pengguna</h2>
                <p class="truncate text-sm text-slate-500">Perbarui informasi dasar dan role akses.</p>
            </div>
        </div>
        <button type="button"
                onclick="window.closeModal()"
                class="ml-4 inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-slate-400 transition-colors duration-150 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="max-h-[70vh] space-y-5 overflow-y-auto px-6 py-5">
        <div id="form-error" class="hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600"></div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Akun Aktif</p>
                    <p class="mt-1 text-base font-semibold text-slate-800">{{ $user->name }}</p>
                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @forelse($userRoles as $role)
                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $roleToneMap[$role] ?? 'border-slate-200 bg-white text-slate-700' }}">
                            {{ ucfirst($role) }}
                        </span>
                    @empty
                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-500">
                            Belum ada role
                        </span>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Nama Pengguna
                </label>
                <input type="text"
                    name="name"
                    value="{{ $user->name }}"
                    required
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                <span class="mt-1 block text-xs text-red-500 error-name"></span>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Email
                </label>
                <input type="email"
                    name="email"
                    value="{{ $user->email }}"
                    required
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                <span class="mt-1 block text-xs text-red-500 error-email"></span>
            </div>
        </div>

        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                Password Baru
            </label>
            <input type="password"
                name="password"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
                placeholder="Kosongkan jika tidak ingin mengubah password">
            <p class="mt-1 text-xs text-slate-400">Biarkan kosong jika password lama tetap digunakan.</p>
            <span class="mt-1 block text-xs text-red-500 error-password"></span>
        </div>

        <div>
            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                Role Pengguna
            </label>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @foreach($availableRoles as $role)
                    <label class="group flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 transition hover:border-sky-300 hover:bg-sky-50/60">
                        <input
                            type="checkbox"
                            id="edit-role-{{ $role }}"
                            name="roles[]"
                            value="{{ $role }}"
                            @if(in_array($role, $userRoles)) checked @endif
                            class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500"
                        >
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-semibold text-slate-700">{{ ucfirst($role) }}</span>
                            <span class="block text-xs text-slate-400">
                                {{ $role === 'admin' ? 'Akses penuh pengelolaan sistem.' : ($role === 'ketua' ? 'Persetujuan dan pengawasan data utama.' : ($role === 'bendahara' ? 'Akses transaksi dan keuangan koperasi.' : 'Akses area anggota dan data pribadi.')) }}
                            </span>
                        </span>
                    </label>
                @endforeach
            </div>
            <span class="mt-1 block text-xs text-red-500 error-roles"></span>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 rounded-b-2xl border-t border-slate-100 bg-slate-50 px-6 py-4">
        <button type="button"
                onclick="window.closeModal()"
                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-400">
            Batal
        </button>
        <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
            Simpan Perubahan
        </button>
    </div>
</form>

<script>
    if (!window.__formEditUserSubmitBound) {
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.id !== 'form-edit-user') return;

            e.preventDefault();

            form.querySelectorAll('[class^="error-"]').forEach(el => el.textContent = '');

            const errorDiv = document.getElementById('form-error');
            if (errorDiv) {
                errorDiv.classList.add('hidden');
                errorDiv.textContent = '';
            }

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                if (res.status === 422) {
                    const data = await res.json();

                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const el = form.querySelector('.error-' + key);
                            if (el) el.textContent = data.errors[key][0];
                        });
                    }

                    return null;
                }

                if (!res.ok) {
                    throw new Error('Gagal menyimpan perubahan.');
                }

                return res.json();
            })
            .then(data => {
                if (data && data.success) {
                    window.closeModal();
                    window.location.reload();
                }
            })
            .catch(error => {
                if (errorDiv) {
                    errorDiv.textContent = error.message;
                    errorDiv.classList.remove('hidden');
                }
            });
        });

        window.__formEditUserSubmitBound = true;
    }
</script>