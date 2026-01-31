<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMPATIK - BPS Provinsi Banten</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeInUp 1s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
        .delay-1 { animation-delay: 0.2s; opacity: 0; }
        .delay-2 { animation-delay: 0.4s; opacity: 0; }
        
        @keyframes subtleFloat {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-15px, 15px); }
        }
        .animate-mesh { animation: subtleFloat 15s infinite ease-in-out; }
    </style>
</head>
<body class="bg-[#fafafa] font-sans antialiased text-[#1e293b] min-h-screen flex flex-col selection:bg-[#f1e4c3]">

    <div class="fixed inset-0 -z-10 overflow-hidden bg-[#fafafa]">
        <div class="absolute top-[-10%] left-[-10%] w-[800px] h-[800px] bg-[#fdf2d1]/30 rounded-full blur-[130px] animate-mesh"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[800px] h-[800px] bg-[#e2e8f0]/40 rounded-full blur-[130px] animate-mesh" style="animation-delay: -7s;"></div>
    </div>

    <main class="flex-grow flex flex-col items-center justify-center px-6 text-center">
        
        <div class="flex items-center justify-center gap-8 mb-10 animate-fade-up">
            <img src="{{ asset('images/bps-hitam.png') }}" alt="Logo BPS Banten" class="h-12 md:h-16 w-auto object-contain">
            <div class="h-10 w-[1px] bg-slate-300"></div>
            <img src="{{ asset('images/bisa-hitam.png') }}" alt="Logo BISA" class="h-12 md:h-16 w-auto object-contain">
        </div>

        <div class="mb-12 animate-fade-up">
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight text-[#0f172a]">
                SIM<span class="text-[#b59441] font-light italic">PATIK</span>
            </h1>
        </div>

        <div class="space-y-8 mb-16 animate-fade-up delay-1">
            <h2 class="text-4xl md:text-7xl font-extrabold tracking-tight text-[#0f172a] leading-tight">
                Dari Kita, Oleh Kita,<br>
                <span class="text-[#b59441]">Untuk Kita.</span>
            </h2>
            
            <p class="text-lg md:text-2xl font-light text-[#64748b] tracking-wide italic">
                "Ikhlas dari <span class="text-[#0f172a] font-medium underline decoration-[#b59441]/30 decoration-2 underline-offset-4">Anda</span> — Halal bagi <span class="text-[#b59441] font-medium">Kita</span>"
            </p>
        </div>

        <div class="animate-fade-up delay-2">
            <a href="{{ route('login') }}" class="group relative inline-flex items-center justify-center px-14 py-5 bg-[#0f172a] text-[#f8fafc] rounded-full font-bold shadow-[0_20px_40px_rgba(15,23,42,0.2)] hover:shadow-[0_20px_50px_rgba(181,148,65,0.25)] hover:-translate-y-1.5 transition-all duration-500 overflow-hidden tracking-[0.15em] text-sm md:text-base">
                <div class="absolute inset-0 w-1/2 h-full bg-white/5 skew-x-[-25deg] -translate-x-full group-hover:translate-x-[250%] transition-transform duration-1000"></div>
                <span class="relative">MASUK KE APLIKASI</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-4 relative group-hover:translate-x-2 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="#b59441">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

    </main>

    <footer class="py-10 text-center animate-fade-up delay-2">
        <div class="backdrop-blur-sm bg-white/20 inline-block px-8 py-3 rounded-full border border-slate-200/50 shadow-sm">
            <p class="text-[10px] md:text-[11px] text-[#64748b] font-bold uppercase tracking-[0.3em]">
                Koperasi Simpan Pinjam BPS Provinsi Banten &copy; 2026
            </p>
        </div>
    </footer>

</body>
</html>