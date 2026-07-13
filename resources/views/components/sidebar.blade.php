<script>
    if (localStorage.getItem('sidebarState') === 'collapsed') {
        document.write(`
            <style id="anti-flicker-style">
                :root { --sidebar-width: 0px; }
                @media (min-width: 768px) { :root { --sidebar-width: 70px !important; } }
                #main-sidebar { width: var(--sidebar-width) !important; }
                .sidebar-brand-text, .sidebar-text, .sidebar-badge { display: none !important; opacity: 0; }
                #main-sidebar .nav-link { justify-content: center !important; px: 0 !important; }
                #main-sidebar .sidebar-brand { justify-content: center !important; px: 0 !important; }
            </style>
        `);
    }
</script>

<aside id="main-sidebar" x-data="{
    isCollapsed: localStorage.getItem('sidebarState') === 'collapsed',
    toggleSidebar() {
        this.isCollapsed = !this.isCollapsed;
        localStorage.setItem('sidebarState', this.isCollapsed ? 'collapsed' : 'expanded');
        document.getElementById('anti-flicker-style')?.remove();

        // 🛠️ Tambahkan baris ini untuk memberi tahu layout utama secara real-time
        this.$dispatch('sidebar-state-changed', { collapsed: this.isCollapsed });
    }
}" @toggle-sidebar-mobile.window="toggleSidebar()"
    :class="isCollapsed ? 'w-0 md:w-[70px]' : 'w-[260px]'"
    class="bg-slate-900 border-r border-slate-800 text-slate-100 flex flex-col justify-between h-screen fixed top-0 left-0 z-50 transition-[width] duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] overflow-hidden">

    <div>
        <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800"
            :class="isCollapsed ? 'md:justify-center md:px-0' : 'px-5'">

            <div class="flex items-center space-x-2 text-white sidebar-brand-text" x-show="!isCollapsed"
                x-transition.opacity>
                <span class="font-extrabold text-base tracking-tight">SIM<span class="text-blue-500">KTS</span></span>
            </div>

            <!-- 🛠️ Hapus window.location.reload() dari directive @click -->
            <button @click="toggleSidebar()"
                class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
        </div>

        <nav class="mt-4 px-3 space-y-1">

            <a href="{{ route('admin.dashboard') }}" title="Dashboard"
                class="flex items-center rounded-xl text-xs font-semibold tracking-wide transition duration-150 group {{ Request::is('admin') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}"
                :class="isCollapsed ? 'justify-center p-3' : 'space-x-3 px-4 py-3'">
                <img src="{{ asset('assets/icon/apps.png') }}" alt="Dashboard"
                    class="w-4 h-4 object-contain opacity-60 group-hover:scale-110 transition duration-150 shrink-0">
                <span class="sidebar-text truncate" x-show="!isCollapsed" x-transition.opacity>Dashboard</span>
            </a>

            <a href="{{ route('admin.kamar.index') }}" title="Data Kamar"
                class="flex items-center rounded-xl text-xs font-semibold tracking-wide transition duration-150 group {{ Request::is('admin/kamar*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}"
                :class="isCollapsed ? 'justify-center p-3' : 'space-x-3 px-4 py-3'">
                <img src="{{ asset('assets/icon/door-open.png') }}" alt="Data Kamar"
                    class="w-4 h-4 object-contain opacity-60 group-hover:scale-110 transition duration-150 shrink-0">
                <span class="sidebar-text truncate" x-show="!isCollapsed" x-transition.opacity>Data Kamar</span>
            </a>

            <a href="{{ route('admin.penghuni.index') }}" title="Data Penghuni"
                class="flex items-center rounded-xl text-xs font-semibold tracking-wide transition duration-150 group {{ Request::is('admin/penghuni*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}"
                :class="isCollapsed ? 'justify-center p-3' : 'space-x-3 px-4 py-3'">
                <img src="{{ asset('assets/icon/member-search.png') }}" alt="Data Penghuni"
                    class="w-4 h-4 object-contain opacity-60 group-hover:scale-110 transition duration-150 shrink-0">
                <span class="sidebar-text truncate" x-show="!isCollapsed" x-transition.opacity>Data Penghuni</span>
            </a>

            <a href="{{ route('admin.booking.index') }}" title="Booking Masuk"
                class="flex items-center rounded-xl text-xs font-semibold tracking-wide transition duration-150 group {{ Request::is('admin/booking*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}"
                :class="isCollapsed ? 'justify-center p-3' : 'justify-between px-4 py-3'">

                <div class="flex items-center space-x-3 min-w-0">
                    <img src="{{ asset('assets/icon/reservation-table.png') }}" alt="Booking"
                        class="w-4 h-4 object-contain opacity-60 group-hover:scale-110 transition duration-150 shrink-0">
                    <span class="sidebar-text truncate" x-show="!isCollapsed" x-transition.opacity>Booking Masuk</span>
                </div>

                @php
                    $jmlBooking = \App\Models\Booking::where('status', 'pending')->count();
                @endphp
                @if ($jmlBooking > 0)
                    <span class="px-2 py-0.5 text-[9px] font-black bg-rose-500 text-white rounded-md shadow-sm shrink-0"
                        x-show="!isCollapsed" x-transition.opacity>
                        {{ $jmlBooking }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.tagihan.index') }}" title="Data Tagihan"
                class="flex items-center rounded-xl text-xs font-semibold tracking-wide transition duration-150 group {{ Request::is('admin/tagihan*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}"
                :class="isCollapsed ? 'justify-center p-3' : 'space-x-3 px-4 py-3'">
                <img src="{{ asset('assets/icon/comments-dollar.png') }}" alt="Data Tagihan"
                    class="w-4 h-4 object-contain opacity-60 group-hover:scale-110 transition duration-150 shrink-0">
                <span class="sidebar-text truncate" x-show="!isCollapsed" x-transition.opacity>Data Tagihan</span>
            </a>

            <div class="border-t border-slate-800/60 my-4"></div>

            <a href="{{ route('admin.pengaturan') }}" title="Pengaturan"
                class="flex items-center rounded-xl text-xs font-semibold tracking-wide transition duration-150 group {{ Request::is('admin/setting*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}"
                :class="isCollapsed ? 'justify-center p-3' : 'space-x-3 px-4 py-3'">
                <img src="{{ asset('assets/icon/settings.png') }}" alt="Pengaturan"
                    class="w-4 h-4 object-contain opacity-60 group-hover:scale-110 transition duration-150 shrink-0">
                <span class="sidebar-text truncate" x-show="!isCollapsed" x-transition.opacity>Pengaturan</span>
            </a>

            <a href="{{ route('admin.laporan') }}" title="Laporan"
                class="flex items-center rounded-xl text-xs font-semibold tracking-wide transition duration-150 group {{ Request::is('admin/laporan*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}"
                :class="isCollapsed ? 'justify-center p-3' : 'space-x-3 px-4 py-3'">
                <img src="{{ asset('assets/icon/newspaper.png') }}" alt="Laporan"
                    class="w-4 h-4 object-contain opacity-60 group-hover:scale-110 transition duration-150 shrink-0">
                <span class="sidebar-text truncate" x-show="!isCollapsed" x-transition.opacity>Laporan</span>
            </a>
        </nav>
    </div>

    <div class="p-4 border-t border-slate-800/60 bg-slate-950/20 text-center" x-show="!isCollapsed"
        x-transition.opacity>
        <span class="text-[10px] font-bold text-slate-500 tracking-wider uppercase">SIMKTS v2.0 Production</span>
    </div>
</aside>
