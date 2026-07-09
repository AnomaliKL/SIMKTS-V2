<footer class="py-4 mt-auto border-t border-slate-800/60 bg-transparent print:hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between text-xs font-medium text-slate-500">
            <div>
                Copyright &copy; {{ date('Y') }} <strong class="text-slate-400 font-bold">SIMKTS (3 Saudara)</strong>
            </div>
            <div class="text-[10px] tracking-wider text-slate-600 uppercase">
                All Rights Reserved
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('click', function(e) {
        // Deteksi jika yang diklik adalah tombol/elemen di dalam tombol '.btn-hapus'
        const target = e.target.closest('.btn-hapus');

        if (target) {
            e.preventDefault(); // Mencegah form atau link tereksekusi langsung

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Merah (rose-500)
                cancelButtonColor: '#64748b',  // Abu-abu (slate-500)
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: '#0f172a',         // Warna bg-slate-900 agar sinkron dengan tema gelap
                color: '#f8fafc',              // Warna teks text-slate-50
                iconColor: '#f59e0b',          // Warna ikon amber-500
                customClass: {
                    popup: 'border border-slate-800 rounded-2xl shadow-2xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // JIKA GUNAKAN FORM (Sangat direkomendasikan di Laravel):
                    // Tombol hapus diletakkan di dalam tag <form action="..." method="POST"> dengan @method('DELETE')
                    const form = target.closest('form');
                    if (form) {
                        form.submit();
                    } else {
                        // Opsi Cadangan: Jika terpaksa menggunakan tag <a> href biasa
                        const href = target.getAttribute('href');
                        if (href) {
                            window.location.href = href;
                        }
                    }
                }
            })
        }
    });
</script>