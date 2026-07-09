<header class="bg-slate-900 border-b border-slate-800/60 h-16 px-6 sticky top-0 z-30 backdrop-blur-md flex items-center justify-between"
        x-data="{ dropdownOpen: false }">
    
    <div class="flex items-center md:hidden">
        <button class="text-slate-400 hover:text-white transition focus:outline-none" 
                id="sidebarToggleMobile">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <div class="hidden md:block"></div>

    <div class="relative">
        <button @click="dropdownOpen = !dropdownOpen" 
                @click.away="dropdownOpen = false"
                class="flex items-center space-x-3 focus:outline-none cursor-pointer group py-1">
            
            @if(Auth::user()->foto_user && file_exists(public_path('storage/' . Auth::user()->foto_user)))
                <img src="{{ asset('storage/' . Auth::user()->foto_user) }}" 
                     alt="Foto Profil" 
                     class="w-9 h-9 rounded-full object-cover border border-slate-700 group-hover:border-blue-500 transition shadow-sm">
            @else
                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-sm uppercase shadow-md border border-blue-500/30">
                    {{ strtoupper(substr(Auth::user()->name ?? Auth::user()->email, 0, 1)) }}
                </div>
            @endif

            <div class="hidden md:block text-left leading-tight">
                <span class="text-xs font-bold text-slate-200 block group-hover:text-blue-400 transition">
                    {{ Auth::user()->name ?? Auth::user()->email }}
                </span>
                <span class="text-[10px] text-slate-500 block font-medium capitalize">
                    {{ Auth::user()->role }}
                </span>
            </div>

            <svg class="w-3 h-3 text-slate-500 transition group-hover:text-slate-300" 
                 :class="{'rotate-180': dropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div x-show="dropdownOpen" 
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute right-0 mt-2.5 w-48 bg-slate-900 border border-slate-800 rounded-xl shadow-2xl py-1.5 z-50 overflow-hidden"
             x-cloak>
            
            <a href="{{ route('profile.edit') }}" 
               class="flex items-center space-x-2.5 px-4 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-800/60 hover:text-white transition">
                <span>⚙️</span>
                <span>Edit Profil</span>
            </a>

            <div class="border-t border-slate-800/60 my-1"></div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="w-full flex items-center space-x-2.5 px-4 py-2 text-xs font-bold text-rose-400 hover:bg-rose-500/10 text-left cursor-pointer transition">
                    <span>Keluar Akun</span>
                </button>
            </form>
        </div>
    </div>
</header>