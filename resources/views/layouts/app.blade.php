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

        /* 🛠️ TRIK ANTI-KEDIP AMAN: Mengunci padding jika localstorage terdeteksi collapsed */
        html.sidebar-is-collapsed .main-layout-content {
            padding-left: 0px !important;
        }

        @media (min-width: 768px) {
            html.sidebar-is-collapsed .main-layout-content {
                padding-left: 70px !important;
            }
        }
    </style>

    <!-- 🛠️ Blocking Script Ringan (Dipasang di head agar dieksekusi sebelum render halaman baru) -->
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
    sidebarCollapsed: localStorage.getItem('sidebarState') === 'collapsed'
}"
    @sidebar-state-changed.window="sidebarCollapsed = $event.detail.collapsed; if($event.detail.collapsed) { document.documentElement.classList.add('sidebar-is-collapsed') } else { document.documentElement.classList.remove('sidebar-is-collapsed') }">

    @include('components.sidebar')

    <!-- Ditambahkan class 'main-layout-content' untuk target CSS anti-kedip di atas -->
    <div :class="sidebarCollapsed ? 'md:pl-[70px]' : 'pl-0 md:pl-[260px]'"
        class="main-layout-content flex-1 flex flex-col min-w-0 h-screen transition-[padding] duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] overflow-hidden">

        @include('components.topbar')

        <main class="flex-1 overflow-y-auto bg-slate-950/40 p-4 sm:p-6 lg:p-8 custom-scrollbar">

            <div class="max-w-7xl mx-auto w-full animate-fade-in">
                @yield('content')
            </div>

        </main>

        @include('components.footer')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebarElement = document.getElementById('main-sidebar');
            if (sidebarElement) {
                // MutationObserver bawaan kamu dikembalikan 100%
                const observer = new MutationObserver(() => {
                    const isCollapsed = sidebarElement.classList.contains('w-0') || sidebarElement.classList
                        .contains('md:w-[70px]');
                    window.dispatchEvent(new CustomEvent('sidebar-state-changed', {
                        detail: {
                            collapsed: isCollapsed
                        }
                    }));
                });
                observer.observe(sidebarElement, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }
        });
    </script>
</body>

</html>
