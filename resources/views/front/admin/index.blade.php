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

                <a href="{{ route('admin.package') }}"
                    class="nav-item @if (request()->routeIs('admin.package')) active @endif">
                    <i data-lucide="package"></i>
                    <span>Package</span>
                </a>

                <a href="{{ route('admin.member') }}"
                    class="nav-item @if (request()->routeIs('admin.member')) active @endif">
                    <i data-lucide="users"></i>
                    <span>Member</span>
                </a>

                <a href="{{ route('admin.sponsor') }}"
                    class="nav-item @if (request()->routeIs('admin.sponsor')) active @endif">
                    <i data-lucide="ticket-slash"></i>
                    <span>Sponsor</span>
                </a>

                <a href="{{ route('admin.viewHistoryTickets') }}"
                    class="nav-item @if (request()->routeIs('admin.viewHistoryTickets')) active @endif">
                    <i data-lucide="ticket"></i>
                    <span>History Tiket Keluar</span>
                </a>

                <a href="{{ route('admin.shift') }}"
                    class="nav-item @if (request()->routeIs('admin.shift')) active @endif">
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
                <div class="closecashier-columns">
                    <!-- Left Column -->
                    <div class="col-left">
                        <div class="closecashier-report">
                            <p><strong>Report:</strong></p>
                            <div style="display: flex; gap: 10px;">
                                <button id="btnExportReport" class="btn-secondary">Export Excel</button>
                                <button id="btnExportPdf" class="btn-secondary">Export PDF</button>
                            </div>
                        </div>

                        <div class="info-section">
                            <p><strong>Staff:</strong> {{ $staff->name ?? '—' }}</p>

                            <p><strong>Waktu Buka:</strong>
                                {{ isset($cashSession->waktu_buka) ? \Carbon\Carbon::parse($cashSession->waktu_buka)->format('H:i | d M Y') : '—' }}
                            </p>
                            <p><strong>Waktu Closing:</strong>
                                <span id="closeCashierTime">{{ now()->format('H:i | d M Y') }}</span>
                            </p>

                            <p><strong>Saldo Awal:</strong> Rp
                                {{ number_format($cashSession->saldo_awal ?? 0, 0, ',', '.') }}
                            </p>
                            <p><strong>Saldo Penjualan Tiket Tunai: </strong> Rp.
                                {{ number_format($purchaseTunai ?? 0, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="closecashier-form">
                            <label for="penjualan_fnb_kolam">Saldo Penjualan F&B Kolam Tunai :</label>
                            <input type="text" id="penjualan_fnb_kolam_display" class="closecashier-input"
                                placeholder="Rp. 0" value="Rp. 0">
                            <input type="hidden" id="penjualan_fnb_kolam" value="0">

                            <label for="penjualan_fnb_cafe">Saldo penjualan F&B Cafe Tunai :</label>
                            <input type="text" id="penjualan_fnb_cafe_display" class="closecashier-input"
                                placeholder="Rp. 0" value="Rp. 0">
                            <input type="hidden" id="penjualan_fnb_cafe" value="0">
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-right">
                        <div class="closecashier-form">
                            <label>Uang Masuk / Cash In Tunai :</label>
                            <div id="cashInList" class="cashinout-list"></div>
                            <button type="button" id="btnAddCashInRow" class="btn-secondary">Tambah Cash In</button>

                            <label style="margin-top: 15px;">Uang Keluar / Cash Out Tunai :</label>
                            <div id="cashOutList" class="cashinout-list"></div>
                            <button type="button" id="btnAddCashOutRow" class="btn-secondary">Tambah Cash Out</button>

                            <label for="saldo_akhir" style="margin-top: 20px;">Saldo Akhir (otomatis):</label>
                            <input type="text" id="saldo_akhir_display" class="closecashier-input" placeholder="Rp. 0"
                                value="Rp. 0" readonly>
                            <input type="hidden" id="saldo_akhir" value="0">
                        </div>
                    </div>
                </div>


            </div>

            <div class="closecashier-footer">
                <button id="btnPrintSummary" class="btn-secondary" style="width: auto;">Print Summary</button>
                <button id="btnCloseModal" class="btn-danger">Batalkan</button>
                <button id="btnProcessClose" class="btn-success">Tutup Kasir & Logout</button>
            </div>
        </div>
    </div>
    </div>

    <!-- 🔹 Print Template (Hidden on Screen, Visible on Print) -->
    <div id="print-area" class="print-area">
        <div class="print-header">
            <h3>Report Summary Transaksi Kasir</h3>
        </div>
        <div class="print-body">
            <div class="print-row">
                <span>Nama Kasir :</span>
                <span>{{ $staff->name ?? '—' }}</span>
            </div>
            <div class="print-row">
                <span>Waktu Buka :</span>
                <span>{{ isset($cashSession->waktu_buka) ? \Carbon\Carbon::parse($cashSession->waktu_buka)->format('H:i | d M Y') : '—' }}</span>
            </div>
            <div class="print-row">
                <span>Waktu Tutup :</span>
                <span id="printCloseTime">{{ now()->format('H:i | d M Y') }}</span>
            </div>

            <hr class="print-divider">

            <div class="print-row">
                <span>Tunai</span>
                <span id="printTunai">Rp. {{ number_format($purchaseTunai ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="print-row">
                <span>Qris BCA</span>
                <span>Rp. {{ number_format($purchaseQrisBca ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="print-row">
                <span>Debit BCA</span>
                <span>Rp. {{ number_format($purchaseDebitBca ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="print-row">
                <span>Qris Mandiri</span>
                <span>Rp. {{ number_format($purchaseQrisMandiri ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="print-row">
                <span>Debit Mandiri</span>
                <span>Rp. {{ number_format($purchaseDebitMandiri ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="print-row">
                <span>Qris BRI</span>
                <span>Rp. {{ number_format($purchaseQrisBri ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="print-row">
                <span>Debit BRI</span>
                <span>Rp. {{ number_format($purchaseDebitBri ?? 0, 0, ',', '.') }}</span>
            </div>

            <br>
            <div class="print-section-title">Summary Tutup Kasir :</div>

            <div class="print-row">
                <span>Saldo Awal :</span>
                <span id="printSaldoAwal">Rp. {{ number_format($cashSession->saldo_awal ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="print-row">
                <span>Penjualan Tunai Tiket</span>
                <span id="printPenjualanTiket">Rp. {{ number_format($purchaseTunai ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="print-row">
                <span>Penjualan Tunai F&B Kolam</span>
                <span id="printFnbKolam">Rp. 0</span>
            </div>
            <div class="print-row">
                <span>Penjualan Tunai F&B Cafe</span>
                <span id="printFnbCafe">Rp. 0</span>
            </div>
            <div class="print-row">
                <span>Cash in Tunai</span>
                <span id="printCashIn">Rp. 0</span>
            </div>
            <!-- Rincian Cash In -->
            <div id="printCashInDetails" class="print-details"></div>

            <div class="print-row">
                <span>Cash Out Tunai</span>
                <span id="printCashOut">Rp. 0</span>
            </div>
            <!-- Rincian Cash Out -->
            <div id="printCashOutDetails" class="print-details"></div>
            <hr class="print-divider">
            <div class="print-row bold">
                <span>Saldo Akhir</span>
                <span id="printSaldoAkhir">Rp. 0</span>
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
        window.CashierData = {
            saldoAwal: {{ (int) ($cashSession->saldo_awal ?? 0) }},
            purchaseTunai: {{ (int) ($purchaseTunai ?? 0) }}
        };
    </script>
    @stack('scripts')
</body>

</html>