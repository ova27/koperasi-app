<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Koperasi Simpatik')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white border-r shadow-sm px-4 pt-4 pb-6">

        @role('admin')
            @include('layouts.sidebar.admin')
        @elserole('anggota')
            @include('layouts.sidebar.anggota')
        @endrole

    </aside>


    {{-- WRAPPER KANAN --}}
    <div class="flex-1 flex flex-col">

        {{-- TOP NAVIGATION --}}
        <header class="bg-white border-b shadow-sm">
            @include('layouts.navigation')
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 p-6">
            <div class="max-w-7xl mx-auto">

                {{-- PAGE TITLE --}}
                @hasSection('page-title')
                    <h1 class="text-xl font-semibold mb-4">
                        @yield('page-title')
                    </h1>
                @endif

                {{-- CARD CONTENT --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    @yield('content')
                </div>

            </div>
        </main>

    </div>

</div>

{{-- ================= MODAL DETAIL ANGGOTA ================= --}}
<div id="modal-detail"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">

    <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg p-6 relative">

        {{-- TOMBOL TUTUP --}}
        <button
            type="button"
            onclick="closeModal()"
            class="absolute top-3 right-3 text-gray-400 hover:text-black text-lg">
            ✕
        </button>

        {{-- KONTEN AJAX --}}
        <div id="modal-content">
            {{-- diisi via fetch --}}
        </div>

    </div>
</div>

{{-- ================= MODAL KONFIRMASI STATUS ================= --}}
<div id="modal-confirm-status"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">

    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">

        <h3 class="text-lg font-semibold text-red-600 mb-2">
            Konfirmasi Pensiun / Mutasi
        </h3>

        <p class="text-sm text-gray-700 mb-4">
            Anda akan mengubah status anggota menjadi
            <b>Pensiun / Mutasi</b>.<br>
            Tindakan ini akan:
        </p>

        <ul class="text-sm text-gray-600 list-disc list-inside mb-4">
            <li>Menonaktifkan akses login anggota</li>
            <li>Menghentikan seluruh transaksi</li>
            <li>Menjadikan anggota sebagai arsip</li>
        </ul>

        <div class="bg-yellow-50 border border-yellow-300 p-3 rounded mb-4 text-sm">
            <b>Pastikan:</b> seluruh simpanan anggota
            <u>sudah dikembalikan</u>.
        </div>

        <div class="flex gap-3 justify-end">
            <button type="button"
                    onclick="closeConfirmStatus()"
                    class="px-4 py-2 bg-gray-200 rounded">
                Batal
            </button>

            <button type="button"
                    onclick="confirmStatusChange()"
                    class="px-4 py-2 bg-red-600 text-white rounded">
                Ya, Simpanan Sudah Dikembalikan
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // EVENT DELEGATION (aman untuk modal & dynamic content)
    document.addEventListener('click', function (e) {

        const detailBtn = e.target.closest('.btn-detail');
        const editBtn   = e.target.closest('.btn-edit');

        if (detailBtn) {
            e.preventDefault();
            openModal(detailBtn.dataset.url);
        }

        if (editBtn) {
            e.preventDefault();
            openModal(editBtn.dataset.url);
        }

    });

});

function openModal(url) {
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.text())
    .then(html => {
        document.getElementById('modal-content').innerHTML = html;

        const modal = document.getElementById('modal-detail');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    })
    .catch(() => {
        alert('Gagal memuat data.');
    });
}

function closeModal() {
    const modal = document.getElementById('modal-detail');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('modal-content').innerHTML = '';
}
</script>

<script>
    let pendingSubmitForm = null;

    document.addEventListener('DOMContentLoaded', () => {

        document.addEventListener('submit', function (e) {

            const form = e.target;
            if (form.id !== 'form-edit-anggota') return;

            const statusSelect = form.querySelector('select[name="status"]');
            if (!statusSelect) return;

            // target status = tidak_aktif (Pensiun / Mutasi)
            if (statusSelect.value === 'tidak_aktif') {
                e.preventDefault(); // tahan submit

                pendingSubmitForm = form;

                openConfirmStatusModal();
            }
        });

    });

    function openConfirmStatusModal() {
        const modal = document.getElementById('modal-confirm-status');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeConfirmStatus() {
        const modal = document.getElementById('modal-confirm-status');
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        pendingSubmitForm = null;
    }

    function confirmStatusChange() {
        if (pendingSubmitForm) {
            pendingSubmitForm.submit(); // lanjut submit asli
        }
    }
</script>


</body>
</html>
