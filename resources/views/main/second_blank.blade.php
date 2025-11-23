<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pacific Pool - Summer Eiretion</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body>
    <nav class="navbar" id="navbar">
        <a href="/">
            <div class="container-fluid">
                <img src="{{ asset('aset/img/logo-pacific-wide.png') }}" class="main-logo">
            </div>
        </a>
    </nav>

    <main>
        @yield('content')
    </main>

    {{-- <footer class="footer" id="footer">
        <div class="footer-content">
            <!-- Payment Section - Left Side -->
            <div class="payment-section">
                <p class="payment-title">Menerima pembayaran secara tunai dan non tunai</p>
                <div class="payment-logos">
                    <img src="/aset/img/logo-qris.png" alt="QRIS" class="payment-logo">
                    <img src="/aset/img/logo-bca.png" alt="BCA" class="payment-logo">
                    <img src="/aset/img/logo-mandiri.png" alt="MANDIRI" class="payment-logo">
                    <img src="/aset/img/logo-bni.png" alt="BNI" class="payment-logo">
                    <img src="/aset/img/logo-bri.png" alt="BRI" class="payment-logo">
                </div>
            </div>

            <!-- Contact Information Section - Right Side -->
            <div class="contact-section">
                <h3 class="contact-title">For More Information about us :</h3>
                <div class="contact-grid">
                    <!-- Phone Numbers -->
                    <div class="contact-item">
                        <i class="fab fa-whatsapp contact-icon whatsapp-icon"></i>
                        <div class="contact-info">
                            <span class="contact-text">0877-2213-3228</span>
                            <span class="contact-text">0817-0885-088</span>
                        </div>
                    </div>

                    <!-- Social Media -->
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

                    <!-- Address -->
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
        </div>
    </footer> --}}
</body>


</html>
