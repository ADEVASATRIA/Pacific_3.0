<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pacific Pool - Login Page</title>

  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  @vite('resources/css/login.css')

  <style>
    /* Modal saldo awal */
    .saldo-modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.6);
      justify-content: center;
      align-items: center;
      z-index: 999;
    }

    .saldo-modal.active {
      display: flex;
    }

    .saldo-content {
      background: #fff;
      padding: 2rem;
      border-radius: 12px;
      width: 400px;
      text-align: center;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    }

    .saldo-content h3 {
      margin-bottom: 1rem;
    }

    .saldo-content input {
      width: 100%;
      padding: .6rem;
      border: 1px solid #ccc;
      border-radius: 6px;
      text-align: center;
      font-size: 1rem;
      margin-bottom: 1rem;
    }

    .saldo-content button {
      width: 100%;
      padding: .6rem;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }

    .saldo-content button:hover {
      background: #0056b3;
    }

    .alert {
      margin-top: 10px;
      color: red;
      font-size: 0.9rem;
    }
  </style>
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
      <input type="number" id="saldoAwal" placeholder="Masukkan nominal saldo awal" min="0" step="1000">
      <button id="submitSaldo">Mulai Shift</button>
    </div>
  </div>

  <script>
    const loginForm = document.getElementById('loginForm');
    const saldoModal = document.getElementById('saldoModal');
    const saldoInput = document.getElementById('saldoAwal');
    const submitSaldo = document.getElementById('submitSaldo');
    const loginError = document.getElementById('loginError');

    // Handle login form
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      const formData = new FormData(loginForm);
      const response = await fetch('{{ route('login.do') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
      });

      const data = await response.json();

      if (!data.success) {
        loginError.textContent = data.message || 'Login gagal.';
        return;
      }

      if (data.role === 'fo') {
        // FO login sukses → tampilkan modal saldo awal
        saldoModal.classList.add('active');
      } else if (data.role === 'bo') {
        window.location.href = '{{ route('dashboard') }}';
      }
    });

    // Handle submit saldo awal
    submitSaldo.addEventListener('click', async () => {
      const saldo = saldoInput.value;
      if (!saldo || saldo < 0) {
        alert('Masukkan saldo awal yang valid.');
        return;
      }

      const response = await fetch('{{ route('cash.store') }}', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ saldo_awal: saldo })
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
