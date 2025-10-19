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


    <script>
        const loginForm = document.getElementById('loginForm');
        const saldoModal = document.getElementById('saldoModal');
        const saldoInput = document.getElementById('saldoAwal');
        const submitSaldo = document.getElementById('submitSaldo');
        const loginError = document.getElementById('loginError');
        const cancelSaldo = document.getElementById('cancelSaldo');

        // Saat tombol batalkan diklik
        cancelSaldo.addEventListener('click', () => {
            saldoModal.classList.remove('active'); // Tutup modal
            window.location.href = '{{ route('login') }}'; // Redirect kembali ke halaman login
        });


        // Fungsi format ke rupiah saat mengetik
        saldoInput.addEventListener('input', function(e) {
            let value = this.value.replace(/[^0-9]/g, ''); // Hanya angka
            if (value) {
                this.value = formatRupiah(value);
            } else {
                this.value = '';
            }
        });

        // Fungsi ubah angka ke format Rupiah
        function formatRupiah(angka) {
            return 'Rp. ' + angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Handle login form
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(loginForm);
            const response = await fetch('{{ route('login.do') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await response.json();

            if (!data.success) {
                loginError.textContent = data.message || 'Login gagal.';
                return;
            }

            if (data.role === 'fo') {
                saldoModal.classList.add('active');
            } else if (data.role === 'bo') {
                window.location.href = '{{ route('dashboard') }}';
            }
        });

        // Handle submit saldo awal
        submitSaldo.addEventListener('click', async () => {
            const rawValue = saldoInput.value.replace(/[^0-9]/g, ''); // Ambil angka murni
            if (!rawValue || rawValue < 0) {
                alert('Masukkan saldo awal yang valid.');
                return;
            }

            const response = await fetch('{{ route('cash.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    saldo_awal: parseInt(rawValue)
                })
            });

            const data = await response.json();
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert('Gagal menyimpan saldo awal.');
            }
        });
    </script>

</body>

</html>
