<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pacific Pool - Summer Eiretion</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body>
    <nav class="navbar">
        <a href="/">
            <div class="container-fluid">
                <img src="{{ asset('aset/img/logo-pacific-wide.png') }}" class="main-logo">
            </div>
        </a>
    </nav>

    {{-- <div class="header-section">
        <div class="logo-container">
            <i class="fas fa-water turtle-icon"></i>
        </div>
        <div class="header-text">
            <h1 class="welcome-title">Welcome to Pacific Pool</h1>
            <p class="subtitle">Selamat Datang di Kolam Renang Pacific Pool</p>
        </div>
    </div> --}}

    {{-- <h2 class="main-title">Welcome To Pacific Pool</h2> --}}

    <div class="cards-grid">
        <div class="service-card" style="--delay: 0.1s;" onclick="handleBuyTicket()">
            <div class="card-header">
                <i class="fas fa-ticket-alt card-icon"></i>
                <h3 class="card-title">Beli Tiket</h3>
            </div>
            <p class="card-description">Masukkan nama dan Telephone untuk pembelian tiket yang akan di beli</p>

            <div class="payment-section">
                <p class="payment-title">Menerima pembayaran secara tunai dan non tunai</p>
                <div class="payment-logos">
                    <img src="aset/img/logo-qris.png" alt="QRIS" class="payment-logo">
                    <img src="aset/img/logo-bca.png" alt="BCA" class="payment-logo">
                    <img src="aset/img/logo-mandiri.png" alt="MANDIRI" class="payment-logo">
                    <img src="aset/img/logo-bni.png" alt="BNI" class="payment-logo">
                    <img src="aset/img/logo-bri.png" alt="BRI" class="payment-logo">
                </div>
            </div>
        </div>

        <div class="service-card" style="--delay: 0.2s;" onclick="handlePrintPackage()">
            <div class="card-header">
                <i class="fas fa-print card-icon"></i>
                <h3 class="card-title">Cetak Tiket Paket</h3>
            </div>

            <p class="card-description">Masukkan nomor telepon anda untuk cetak tiket. Khusus Paket Tiket yang masih
                tersedia</p>
        </div>

        <div class="service-card" style="--delay: 0.3s;" onclick="handlePrintMemberTicket()">
            <div class="card-header">
                <i class="fas fa-id-card card-icon"></i>
                <h3 class="card-title">Cetak Tiket Member</h3>
            </div>
            <p class="card-description">Masukkan Nomor Telephone anda untuk cetak tiket member</p>
        </div>

        <div class="service-card" style="--delay: 0.4s;" onclick="handlePrintTrainerTicket()">
            <div class="card-header">
                <i class="fas fa-user-tie card-icon"></i>
                <h3 class="card-title">Cetak Tiket Pelatih</h3>
            </div>
            <p class="card-description">Masukkan Nomor Telephone anda untuk cetak tiket pelatih</p>
        </div>
    </div>
    <footer class="footer">
        <div class="footer-content">
            <div class="contact-section">
                <h3 class="contact-title">For More Information about us :</h3>
                <div class="contact-grid">
                    <div class="contact-item">
                        <i class="fab fa-whatsapp contact-icon whatsapp-icon"></i>
                        <div class="contact-info">
                            <span class="contact-text">0877-2213-3228</span>
                            <span class="contact-text">0817-0885-088</span>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="social-icons">
                            <div class="social-item">
                                <i class="fab fa-instagram contact-icon instagram-icon"></i>
                                <span class="contact-text">@pacificpoolcirebon</span>
                            </div>
                            <div class="social-item">
                                <i class="fab fa-tiktok contact-icon tiktok-icon"></i>
                                <span class="contact-text">@pacificpoolcirebon</span>
                            </div>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt contact-icon location-icon"></i>
                        <div class="contact-info">
                            <span class="contact-text">Jl. Dewi Sartika No.08</span>
                            <span class="contact-text">Tukmudal, Sumber</span>
                            <span class="contact-text">(Samping MINISO Sumber)</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-wrapper">
                <div class="service-card admin-card" onclick="handleAdminLogin()"
                    style="max-width: 300px; /* Batasi lebar tombol */
                    margin: 0; /* Pastikan tidak ada margin tambahan */
                    cursor: pointer;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Tambah shadow agar menonjol */
                    border: 2px solid #3b82f6;">
                    <h3 class="card-title" style="color: #3b82f6; font-size: 1.25rem;">Admin Pacific</h3>
                    <p class="card-description">Akses ke Dashboard & Management Tiket oleh Staff</p>
                </div>
            </div>

        </div>
    </footer>
    <div id="adminPinModal" class="modal modal-pin hidden">
        <div class="modal-content">
            <div class="lock-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 2C9.243 2 7 4.243 7 7v3H6c-1.103 0-2 .897-2 2v8c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-8c0-1.103-.897-2-2-2h-1V7c0-2.757-2.243-5-5-5zm-3 5c0-1.654 1.346-3 3-3s3 1.346 3 3v3H9V7zm4 10.723V20h-2v-2.277a1.993 1.993 0 0 1-.567-3.677A2.001 2.001 0 0 1 14 14a1.99 1.99 0 0 1-1 1.723z" />
                </svg>
            </div>

            <h3>Masukkan PIN Admin</h3>
            <p class="subtitle">Masukkan 4 digit PIN untuk melanjutkan</p>

            <p id="pin-error">PIN salah. Silakan coba lagi.</p>

            <input type="password" id="adminPinInput" class="pin-input" maxlength="4" placeholder="••••"
                inputmode="numeric" autocomplete="one-time-code">

            <div class="button-group">
                <button class="modal-button cancel" onclick="closeAdminModal()">Batal</button>
                <button id="adminPinSubmitBtn" class="modal-button submit" type="button"
                    onclick="submitPin()">Verifikasi</button>
            </div>
        </div>
    </div>

</body>

</html>
