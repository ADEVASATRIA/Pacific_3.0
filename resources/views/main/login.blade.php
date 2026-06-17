<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pacific Pool - Login Page</title>

    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @vite('resources/css/login.css')
</head>

<body>
    <div class="wrapper">
        <form id="loginForm" role="form text-left">
            @csrf
            <h1>Selamat Datang!</h1>
            <h2>Silahkan melakukan login untuk mengakses.</h2>

            <div class="input-box">
                <i class='bx bxs-envelope'></i>
                <input type="text" name="username" placeholder="Masukkan Username" required>
            </div>

            <div class="input-box">
                <i class='bx bxs-lock-alt'></i>
                <input type="password" name="password" placeholder="Masukkan Password" required>
            </div>

            <button type="submit" class="btn btn-primary">Masuk</button>

            <div id="loginError" class="alert"></div>
        </form>
    </div>

    <!-- Modal Saldo Awal -->
    <div id="saldoModal" class="saldo-modal">
        <div class="saldo-content">
            <h3>Masukkan Saldo Awal</h3>
            <p>Silakan masukkan saldo awal kasir sebelum memulai shift.</p>
            <input type="text" id="saldoAwal" placeholder="Masukkan nominal saldo awal">

            <div class="button-group">
                <button id="submitSaldo">Mulai Shift</button>
                <button id="cancelSaldo" class="cancel-btn" data-dismiss="modal">Batalkan</button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Sesi Terakhir -->
    <div id="sessionConfirmModal" class="saldo-modal">
        <div class="saldo-content">
            <h3>Session terakhir masih belum tertutup</h3>
            <div class="button-group">
                <button id="useLatestSession">Pakai Session Terakhir</button>
                {{-- <button id="createNewSession" class="cancel-btn">Buat Session Baru</button> --}}
            </div>
        </div>
    </div>

    <script>
        // ─── Element References ───────────────────────────────────────────────────
        const loginForm           = document.getElementById('loginForm');
        const saldoModal          = document.getElementById('saldoModal');
        const saldoInput          = document.getElementById('saldoAwal');
        const submitSaldo         = document.getElementById('submitSaldo');
        const loginError          = document.getElementById('loginError');
        const cancelSaldo         = document.getElementById('cancelSaldo');
        const sessionConfirmModal = document.getElementById('sessionConfirmModal');
        const useLatestBtn        = document.getElementById('useLatestSession');
        const createNewBtn        = document.getElementById('createNewSession'); // Mungkin null jika di-comment

        // ─── Helper: Format Rupiah ────────────────────────────────────────────────
        function formatRupiah(angka) {
            return 'Rp. ' + angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // ─── Format input saldo saat mengetik ────────────────────────────────────
        if (saldoInput) {
            saldoInput.addEventListener('input', function () {
                const value = this.value.replace(/[^0-9]/g, '');
                this.value = value ? formatRupiah(value) : '';
            });
        }

        // ─── Tombol Batalkan: tutup modal & kembali ke login ─────────────────────
        if (cancelSaldo) {
            cancelSaldo.addEventListener('click', () => {
                if (saldoModal) saldoModal.classList.remove('active');
                window.location.href = '{{ route('login') }}';
            });
        }

        // ─── Handle login form submit ─────────────────────────────────────────────
        if (loginForm) {
            loginForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                try {
                    const formData = new FormData(loginForm);
                    const response = await fetch('{{ route('login.do') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    });

                    if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

                    const data = await response.json();

                    if (!data.success) {
                        if (loginError) loginError.textContent = data.message || 'Login gagal.';
                        return;
                    }

                    if (data.role === 'fo') {
                        try {
                            const checkResp = await fetch('{{ route('cash.checkLatest') }}', {
                                method: 'GET',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                            });
                            const checkData = await checkResp.json();
                            if (!checkData.success) {
                                if (sessionConfirmModal) sessionConfirmModal.classList.add('active');
                            } else {
                                if (saldoModal) saldoModal.classList.add('active');
                            }
                        } catch (err) {
                            console.error('Gagal memeriksa sesi terakhir:', err);
                            if (saldoModal) saldoModal.classList.add('active');
                        }
                    } else if (data.role === 'bo') {
                        window.location.href = '{{ route('dashboard') }}';
                    }
                } catch (err) {
                    console.error('Login request gagal:', err);
                    if (loginError) loginError.textContent = 'Terjadi kesalahan jaringan. Coba lagi.';
                }
            });
        }

        // ─── Pakai Session Terakhir ───────────────────────────────────────────────
        if (useLatestBtn) {
            useLatestBtn.addEventListener('click', () => {
                if (sessionConfirmModal) sessionConfirmModal.classList.remove('active');
                window.location.href = '{{ route('main') }}';
            });
        }

        // ─── Buat Session Baru (opsional, bisa di-comment di HTML) ───────────────
        if (createNewBtn) {
            createNewBtn.addEventListener('click', () => {
                if (sessionConfirmModal) sessionConfirmModal.classList.remove('active');
                if (saldoModal) saldoModal.classList.add('active');
            });
        }

        // ─── Submit Saldo Awal: Mulai Shift ───────────────────────────────────────
        if (submitSaldo) {
            submitSaldo.addEventListener('click', async () => {
                const rawValue = saldoInput ? saldoInput.value.replace(/[^0-9]/g, '') : '';
                const numericValue = parseInt(rawValue, 10);

                if (!rawValue || isNaN(numericValue) || numericValue < 0) {
                    alert('Masukkan saldo awal yang valid.');
                    return;
                }

                try {
                    const response = await fetch('{{ route('cash.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ saldo_awal: numericValue })
                    });

                    if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

                    const data = await response.json();
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message || 'Gagal menyimpan saldo awal.');
                    }
                } catch (err) {
                    console.error('Gagal menyimpan saldo awal:', err);
                    alert('Terjadi kesalahan jaringan saat menyimpan saldo. Coba lagi.');
                }
            });
        }
    </script>

</body>

</html>
