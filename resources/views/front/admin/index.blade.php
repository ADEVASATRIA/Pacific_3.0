<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Dashboard Admin')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/admin/admin.css', 'resources/css/admin/close-modal.css', 'resources/js/admin/index.js'])
    @stack('styles')
</head>

<body>
    <div class="app">
        <!-- Sidebar -->
        <aside class="sidebar" role="navigation" aria-label="Sidebar">
            <div class="brand">
                <div class="logo">PG</div>
                <div>
                    <h1>Pacific Admin</h1>
                    <p>Dashboard</p>
                </div>
            </div>

            <nav class="menu">

                <a href="{{ route('main') }}" class="nav-item">
                    <i data-lucide="home"></i>
                    <span>Home</span>
                </a>

                <a href="{{ route('admin.transaksi') }}"
                    class="nav-item @if (request()->routeIs('admin.transaksi')) active @endif">
                    <i data-lucide="credit-card"></i>
                    <span>Transaksi</span>
                </a>

                <a href="{{ route('admin.package') }}" class="nav-item @if (request()->routeIs('admin.package')) active @endif">
                    <i data-lucide="package"></i>
                    <span>Package</span>
                </a>

                <a href="{{ route('admin.member') }}" class="nav-item @if (request()->routeIs('admin.member')) active @endif">
                    <i data-lucide="users"></i>
                    <span>Member</span>
                </a>

                <a href="{{ route('admin.sponsor') }}" class="nav-item @if (request()->routeIs('admin.sponsor')) active @endif">
                    <i data-lucide="ticket-slash"></i>
                    <span>Sponsor</span>
                </a>

                <a href="{{ route('admin.viewHistoryTickets') }}"
                    class="nav-item @if (request()->routeIs('admin.viewHistoryTickets')) active @endif">
                    <i data-lucide="ticket"></i>
                    <span>History Tiket Keluar</span>
                </a>

                <a href="{{ route('admin.shift') }}" class="nav-item @if (request()->routeIs('admin.shift')) active @endif">
                    <i data-lucide="shirt"></i>
                    <span>Shift Hari ini</span>
                </a>

                <!-- Tombol Tutup Kasir -->
                <a href="#" id="btnOpenCloseModal" class="nav-item">
                    <i data-lucide="power"></i>
                    <span>Closing</span>
                </a>
            </nav>

            <div class="sidebar-footer">© Pacific Global</div>
        </aside>

        <!-- Main -->
        <main class="main" role="main">
            <div class="content-layer">
                <div class="topbar">
                    <h2>@yield('page-title')</h2>
                    <div class="controls">
                        @yield('top-controls')
                    </div>
                </div>

                {{-- Konten halaman --}}
                @yield('content')
            </div>
        </main>

    </div>

    <!-- 🔹 Modal Tutup Kasir -->
    <div id="closeCashierModal" class="closecashier-modal">
        <div class="closecashier-modal-content">
            <h2>Tutup Kasir</h2>

            <div class="closecashier-body">
                <p><strong>Staff:</strong> {{ $staff->name ?? '—' }}</p>
                <p><strong>Saldo Awal:</strong> Rp{{ number_format($cashSession->saldo_awal ?? 0, 0, ',', '.') }}</p>
                <p><strong>Waktu Buka:</strong>
                    {{ isset($cashSession->waktu_buka) ? \Carbon\Carbon::parse($cashSession->waktu_buka)->format('H:i | d M Y') : '—' }}
                </p>
                <p><strong>Waktu Closing:</strong>
                    <span id="closeCashierTime">{{ now()->format('H:i | d M Y') }}</span>
                </p>

                <div class="closecashier-form">
                    <label for="saldo_akhir">Saldo Akhir (Total Fisik Kas):</label>
                    <!-- Saldo Akhir (formatted Rp) -->
                    <input type="text" id="saldo_akhir_display" class="closecashier-input" placeholder="Rp. 0"
                        value="Rp. 0">
                    <!-- Hidden real value for server -->
                    <input type="hidden" id="saldo_akhir" value="0">

                    <!-- Input baru: FNB Balance -->
                    <label for="fnb_balance">Saldo F&B (Uang F&B):</label>
                    <input type="text" id="fnb_balance_display" class="closecashier-input" placeholder="Rp. 0"
                        value="Rp. 0">
                    <input type="hidden" id="fnb_balance" value="0">

                    <!-- Input baru: Minus Balance -->
                    <label for="minus_balance">Minus Balance (Kekurangan Kas):</label>
                    <input type="text" id="minus_balance_display" class="closecashier-input" placeholder="Rp. 0"
                        value="Rp. 0">
                    <input type="hidden" id="minus_balance" value="0">
                </div>

                <div class="closecashier-report">
                    <p><strong>Report:</strong></p>
                    <button id="btnExportReport" class="btn-secondary">Export Laporan Harian</button>
                </div>
            </div>

            <div class="closecashier-footer">
                <button id="btnCloseModal" class="btn-danger">Batalkan</button>
                <button id="btnProcessClose" class="btn-success">Tutup Kasir & Logout</button>
            </div>
        </div>
    </div>

    <!-- 🔹 Modal Konfirmasi Success (KHUSUS Closing Kasir) -->
    <div id="cashierSuccessModal" class="success-modal">
        <div class="success-modal-content">
            <div class="success-icon">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none" />
                    <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                </svg>
            </div>
            <h3 class="success-title">Kasir Berhasil Ditutup!</h3>
            <p class="success-message">Anda akan logout.</p>
        </div>
    </div>


    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
        window.CashierRoutes = {
            processClose: "{{ route('cashsession.processClose') }}",
            exportReport: "{{ route('cashsession.export') }}"
        };
        window.CsrfToken = "{{ csrf_token() }}";
    </script>
    @stack('scripts')
</body>

</html>
