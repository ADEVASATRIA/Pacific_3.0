@extends('main.blank')
@section('content')
    @vite('resources/css/front/print-ticket-coach.css')
    <div class="ticket-wrapper">
        
        <!-- Informasi Customer -->
        <div class="ticket-card customer-info">
            <h3 class="section-title">Informasi Customer</h3>
            <div class="info-row">
                <span>Nama Customer</span>
                <span>Jupri</span>
            </div>
            <div class="info-row">
                <span>Nama Club House</span>
                <span>Sajojo Club</span>
            </div>
            <div class="info-row">
                <span>Masa Berlaku Tiket</span>
                <span>19 Oktober 2025</span>
            </div>

        </div>

        <!-- Kartu Tiket -->
        <div class="ticket-card">
            <!-- QR Code -->
            <div class="qr-section">
                <img src="{{ asset('/aset/img/test-qr.png') }}" alt="QR Code Tiket">
            </div>

            <!-- Detail Tiket -->
            <div class="ticket-info">
                <h2 class="ticket-title">Pacific Pool - TCP1001</h2>
                <p class="ticket-subtitle">Tiket 1x Pakai</p>
                <p class="ticket-desc">
                    Tiket ini berlaku untuk 1 Orang <br>
                    Berlaku Tanggal <strong>13 Agustus 2023</strong>
                </p>
                <p class="ticket-price">Rp. 40.000</p>
            </div>
        </div>

        <!-- Tombol Print -->
        <div class="print-btn">
            <button onclick="window.print()">Print Tiket</button>
        </div>
    </div>
@endsection
