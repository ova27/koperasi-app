<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Koperasi Simpatik')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">

<div class="flex min-h-screen relative">
    {{-- SIDEBAR --}}
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 w-64 transition-all duration-300 overflow-hidden
        bg-gradient-to-b from-slate-50 to-slate-100 border-r border-gray-200 shadow-lg px-3 pt-5 pb-6
        lg:static lg:translate-x-0 lg:shadow-sm">

        @role('admin')
            @include('layouts.sidebar.admin')
        @elserole('anggota')
            @include('layouts.sidebar.anggota')
        @endrole
    </aside>

    {{-- WRAPPER KANAN --}}
    <div id="mainContent" class="flex-1 flex flex-col transition-all duration-300">

        {{-- TOP NAVIGATION --}}
        <header class="bg-white border-b shadow-sm">
            @include('layouts.navigation')
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 p-6 bg-stone-50 border-r border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto">

                {{-- PAGE TITLE --}}
                @hasSection('page-title')
                    <h1 class="text-xl font-semibold mb-4">
                        @yield('page-title')
                    </h1>
                @endif

                {{-- CARD CONTENT --}}
                <div class="bg-white border border-gray-200 rounded-xl p-6">
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

    function confirmStatusChange() {
        if (pendingSubmitForm) {
            pendingSubmitForm.submit(); // lanjut submit asli
        }
    }
</script>

{{-- TOGGLE SIDEBAR DESKTOP --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');

        if (!sidebar || !toggleBtn) return;

        // load state
        if (localStorage.getItem('sidebar') === 'collapsed') {
            sidebar.classList.add('sidebar-collapsed');
        }

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-collapsed');

            if (sidebar.classList.contains('sidebar-collapsed')) {
                localStorage.setItem('sidebar', 'collapsed');
            } else {
                localStorage.setItem('sidebar', 'expanded');
            }
        });
    });
</script>

</body>
</html>
