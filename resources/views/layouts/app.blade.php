<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard - SIMKTS')</title>
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* 🛠️ Trik Anti-Kedip: Jika terdeteksi collapsed dari awal, kunci padding tanpa menunggu JS */
        html.sidebar-is-collapsed .main-content-wrapper {
            padding-left: 0px !important;
        }

        @media (min-width: 768px) {
            html.sidebar-is-collapsed .main-content-wrapper {
                padding-left: 70px !important;
            }
        }
    </style>

    <!-- 🛠️ Blocking Script: Dijalankan sebelum browser merender elemen HTML -->
    <script>
        (function() {
            if (localStorage.getItem('sidebarState') === 'collapsed') {
                document.documentElement.classList.add('sidebar-is-collapsed');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-950 text-slate-100 antialiased h-full flex overflow-hidden font-sans" x-data="{
    sidebarCollapsed: localStorage.getItem('sidebarState') === 'collapsed',
    toggleSidebar() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
        localStorage.setItem('sidebarState', this.sidebarCollapsed ? 'collapsed' : 'expanded');

        // 🛠️ Sinkronkan class HTML agar transisi awal tetap mulus
        if (this.sidebarCollapsed) {
            document.documentElement.classList.add('sidebar-is-collapsed');
        } else {
            document.documentElement.classList.remove('sidebar-is-collapsed');
        }

        window.dispatchEvent(new CustomEvent('sidebar-toggle', {
            detail: { collapsed: this.sidebarCollapsed }
        }));
    }
}"
    @sidebar-state-changed.window="sidebarCollapsed = $event.detail.collapsed; if($event.detail.collapsed) { document.documentElement.classList.add('sidebar-is-collapsed') } else { document.documentElement.classList.remove('sidebar-is-collapsed') }">

    @include('components.sidebar')

    <!-- 🛠️ Tambahkan class 'main-content-wrapper' untuk pengunci CSS anti-kedip di atas -->
    <div :class="sidebarCollapsed ? 'md:pl-[70px]' : 'pl-0 md:pl-[260px]'"
        class="main-content-wrapper flex-1 flex flex-col min-w-0 h-screen transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] overflow-hidden">

        @include('components.topbar')

        <!-- Berikan state x-data lokal untuk mendeteksi status loading halaman -->
        <main class="flex-1 overflow-y-auto bg-slate-950/40 p-4 sm:p-6 lg:p-8 custom-scrollbar" x-data="{ pageLoading: false }"
            @page-leave.window="pageLoading = true">

            <!-- Bungkus konten utama dengan efek transisi reaktif Alpine -->
            <div class="max-w-7xl mx-auto w-full transition-all duration-300 ease-in-out"
                :class="pageLoading ? 'opacity-0 scale-95 blur-sm' : 'opacity-100 scale-100 blur-0'">
                @yield('content')
            </div>
        </main>

        @include('components.footer')
    </div>

    <!-- Posisikan CDN SweetAlert2 di luar tag block script custom untuk menghindari error browser -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Cari semua tag <a> tautan navigasi internal menu SIMKTS
            document.querySelectorAll('nav a, .flex-1 a').forEach(link => {
                if (link.href && new URL(link.href).origin === window.location.origin && !link.href
                    .includes('logout')) {
                    link.addEventListener('click', function(e) {
                        // Cek jika bukan klik kanan atau open new tab
                        if (e.button === 0 && !e.ctrlKey && !e.metaKey) {
                            e.preventDefault();
                            const targetTarget = this.href;

                            // Memicu event Alpine untuk memulai animasi Fade-Out / menciut
                            window.dispatchEvent(new CustomEvent('page-leave'));

                            // Tunggu animasi CSS selesai (250ms) baru pindahkan halaman resmi
                            setTimeout(() => {
                                window.location.href = targetTarget;
                            }, 250);
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>
