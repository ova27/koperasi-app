{{-- GLOBAL ERROR --}}
<div id="form-error" class="hidden mb-3 text-sm text-red-600"></div>

<form id="form-edit-user"
      method="POST"
      action="{{ route('admin.users.update', $user) }}"
      class="space-y-4">

    @csrf
    @method('PUT')

    {{-- NAMA --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Nama Pengguna
        </label>
        <input type="text"
            name="name"
            value="{{ $user->name }}"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <span class="text-xs text-red-500 error-name"></span>
    </div>

    {{-- EMAIL --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Email
        </label>
        <input type="email"
            name="email"
            value="{{ $user->email }}"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <span class="text-xs text-red-500 error-email"></span>
    </div>

    {{-- PASSWORD --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Password (Kosongkan jika tidak ingin mengubah)
        </label>
        <input type="password"
            name="password"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <span class="text-xs text-red-500 error-password"></span>
    </div>

    {{-- ROLES --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Role
        </label>
        <div class="space-y-2">
            @php
                $userRoles = $user->roles->pluck('name')->toArray();
                $availableRoles = ['admin', 'ketua', 'bendahara', 'anggota'];
            @endphp
            @foreach($availableRoles as $role)
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="edit-role-{{ $role }}"
                        name="roles[]"
                        value="{{ $role }}"
                        @if(in_array($role, $userRoles)) checked @endif
                        class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                    >
                    <label for="edit-role-{{ $role }}" class="ml-2 text-sm text-gray-700 cursor-pointer">
                        {{ ucfirst($role) }}
                    </label>
                </div>
            @endforeach
        </div>
        <span class="text-xs text-red-500 error-roles"></span>
    </div>

    {{-- FOOTER AKSI --}}
    <div class="flex justify-between items-center pt-4 border-t gap-3">
        <button type="button"
                onclick="window.closeModal()"
                class="flex-1 px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium">
            Batal
        </button>
        <button type="submit"
                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            Simpan
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('form-edit-user');
        
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // reset error
            form.querySelectorAll('[class^="error-"]').forEach(el => el.textContent = '');
            const errorDiv = document.getElementById('form-error');
            if (errorDiv) errorDiv.classList.add('hidden');

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                if (res.status === 422) {
                    const data = await res.json();

                    // tampilkan error per field
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const el = form.querySelector('.error-' + key);
                            if (el) el.textContent = data.errors[key][0];
                        });
                    }

                    return;
                }

                if (!res.ok) {
                    throw new Error('Network response was not ok');
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
                console.error('Error:', error);
                const errorDiv = document.getElementById('form-error');
                if (errorDiv) {
                    errorDiv.textContent = 'Terjadi kesalahan: ' + error.message;
                    errorDiv.classList.remove('hidden');
                }
            });
        });
    });
</script>