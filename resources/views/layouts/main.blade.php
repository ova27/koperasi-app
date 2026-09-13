<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Koperasi Simpatik')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="app-body bg-slate-50 text-slate-800">

<div class="flex min-h-screen relative">
    {{-- SIDEBAR --}}

    <aside id="sidebar"
        class="sidebar app-sidebar fixed inset-y-0 left-0 z-40 w-72 overflow-y-auto overflow-x-hidden px-4 pb-8 transition-all duration-300
        lg:static lg:z-auto lg:translate-x-0 lg:shadow-md">

        <!-- Sidebar Header / Logo (moved to very top, removed pt-7 from aside, add pt-7 to header) -->
        <div class="sidebar-brand flex items-center gap-2 mb-4 pt-6">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-blue-100 bg-white p-1 shadow-sm">
                <img src="/images/logo_koperasi.png" alt="Logo Koperasi Simpatik" class="h-full w-full object-contain">
            </span>
            <div class="flex flex-col ml-1 sidebar-header-text">
                <span class="font-bold text-base text-slate-900 tracking-tight whitespace-nowrap">Koperasi Simpatik</span>
                <span class="text-xs text-slate-500 leading-tight">BPS Provinsi Banten</span>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-b border-blue-100 mb-6"></div>

        <!-- Sidebar Menu -->
        <nav class="flex flex-col gap-1">
            @if(auth()->user()->hasAnyRole(['admin', 'ketua', 'bendahara']))
                @include('layouts.sidebar.admin')
            @elseif(auth()->user()->hasRole('anggota'))
                @include('layouts.sidebar.anggota')
            @endif
        </nav>
        
    </aside>

    {{-- OVERLAY MOBILE --}}
    <div id="sidebarOverlay" class="fixed inset-0 z-30 hidden bg-black/40 lg:hidden"></div>

    {{-- WRAPPER KANAN --}}
    <div id="mainContent" class="flex-1 flex flex-col transition-all duration-300">
        {{-- TOP NAVIGATION --}}
        <header class="app-header">
            @include('layouts.navigation')
        </header>

        {{-- CONTENT --}}
        <main class="page-main">
            <div class="mx-auto w-full max-w-[1600px]">
                {{-- PAGE TITLE --}}
                @hasSection('page-title')
                    <div class="page-heading">
                        <div>
                        <h1>
                            @yield('page-title')
                        </h1>

                        @hasSection('page-description')
                            <p>
                                @yield('page-description')
                            </p>
                        @endif
                        </div>
                    </div>
                @endif

                {{-- CARD CONTENT --}}
                <div class="page-card app-content">
                    @yield('content')
                </div>
            </div>
        </main>
        
        <!-- Sidebar Footer (optional) -->
        <div class="mt-auto pt-4 pb-4 text-sm text-slate-400 text-center select-none">
            &copy; {{ date('Y') }} Koperasi Simpatik - BPS Provinsi Banten
        </div>

    </div>

</div>

{{-- ================= MODAL DETAIL ANGGOTA ================= --}}
<div id="modal-detail"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-title">

    {{-- BACKDROP --}}
    <div id="modal-backdrop"
         class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
         onclick="closeModal()"></div>

    {{-- PANEL --}}
    <div id="modal-panel"
         class="relative w-full max-w-2xl rounded-2xl bg-white shadow-2xl ring-1 ring-black/5
                translate-y-4 opacity-0 transition-all duration-300 ease-out overflow-hidden">

        {{-- LOADING STATE --}}
        <div id="modal-loading" class="flex flex-col items-center justify-center py-20 gap-3">
            <svg class="w-8 h-8 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            <p class="text-sm text-slate-500">Memuat data...</p>
        </div>

        {{-- KONTEN AJAX --}}
        <div id="modal-content" class="hidden">
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

    // ESC key to close modal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

});

function openModal(url) {
    const modal   = document.getElementById('modal-detail');
    const panel   = document.getElementById('modal-panel');
    const loading = document.getElementById('modal-loading');
    const content = document.getElementById('modal-content');

    // Reset state
    content.classList.add('hidden');
    content.innerHTML = '';
    loading.classList.remove('hidden');

    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';

    // Animate in
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            panel.classList.remove('translate-y-4', 'opacity-0');
            panel.classList.add('translate-y-0', 'opacity-100');
        });
    });

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
        content.innerHTML = html;
        content.querySelectorAll('script').forEach(oldScript => {
            const newScript = document.createElement('script');

            Array.from(oldScript.attributes).forEach(attribute => {
                newScript.setAttribute(attribute.name, attribute.value);
            });

            newScript.textContent = oldScript.textContent;
            oldScript.replaceWith(newScript);
        });

        loading.classList.add('hidden');
        content.classList.remove('hidden');
    })
    .catch(() => {
        loading.classList.add('hidden');
        content.innerHTML = '<div class="p-6 text-center text-red-500 text-sm">Gagal memuat data. Silakan coba lagi.</div>';
        content.classList.remove('hidden');
    });
}

function closeModal() {
    const modal = document.getElementById('modal-detail');
    const panel = document.getElementById('modal-panel');

    panel.classList.remove('translate-y-0', 'opacity-100');
    panel.classList.add('translate-y-4', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        document.getElementById('modal-content').innerHTML = '';
        document.getElementById('modal-content').classList.add('hidden');
        document.getElementById('modal-loading').classList.remove('hidden');
    }, 200);
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

{{-- TOGGLE SIDEBAR RESPONSIVE --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const desktopBreakpoint = 1024;

        if (!sidebar || !toggleBtn || !sidebarOverlay) return;

        const isDesktop = () => window.innerWidth >= desktopBreakpoint;

        const setMobileOpenState = (isOpen) => {
            sidebar.classList.toggle('mobile-open', isOpen);
            sidebarOverlay.classList.toggle('hidden', !isOpen);
            document.body.classList.toggle('overflow-hidden', isOpen);
        };

        const applyDesktopState = () => {
            sidebar.classList.remove('mobile-open');
            sidebar.classList.remove('sidebar-collapsed');
            sidebarOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        if (isDesktop()) {
            applyDesktopState();
        } else {
            sidebar.classList.remove('sidebar-collapsed');
            setMobileOpenState(false);
        }


        // Helper: hide/show sidebar header text on collapse
        const updateSidebarHeaderText = () => {
            const headerText = sidebar.querySelector('.sidebar-header-text');
            if (sidebar.classList.contains('sidebar-collapsed')) {
                headerText && headerText.classList.add('hidden');
            } else {
                headerText && headerText.classList.remove('hidden');
            }
        };

        toggleBtn.addEventListener('click', () => {
            if (isDesktop()) {
                sidebar.classList.toggle('sidebar-collapsed');
                updateSidebarHeaderText();
                return;
            }

            const isOpen = sidebar.classList.contains('mobile-open');
            setMobileOpenState(!isOpen);
        });

        // Initial state
        updateSidebarHeaderText();

        sidebarOverlay.addEventListener('click', () => {
            setMobileOpenState(false);
        });

        window.addEventListener('resize', () => {
            if (isDesktop()) {
                applyDesktopState();
                return;
            }

            sidebar.classList.remove('sidebar-collapsed');
            setMobileOpenState(false);
        });

        sidebar.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                if (!isDesktop()) {
                    setMobileOpenState(false);
                }
            });
        });
    });
</script>

</body>
</html>
