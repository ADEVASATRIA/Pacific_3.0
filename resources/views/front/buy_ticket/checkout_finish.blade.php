@extends('main.blank')

@section('content')
    @vite('resources/css/front/checkout_finish.css')

    <div class="checkout-container">
        <div class="checkout-card">
            <h1 class="title">Terimakasih</h1>
            <p class="subtitle">
                Pembayaran sudah diterima, silahkan cetak struk dan tiket 
                (untuk Tiket Paket silahkan cetak tiket melalui tombol 
                <b>"Cetak Tiket"</b> di halaman depan)
            </p>

            <p class="refund">Total Kembalian = <span>Rp.{{ number_format($purchase->kembalian, 0, ',', '.') }}</span></p>

            <div class="button-group">
                <button class="btn primary" onclick="printReceipt()">Cetak Struk</button>
                <button class="btn success" id="print-ticket">Cetak Tiket</button>
                <a href="{{ route('main') }}" class="btn secondary">Back To Home</a>
            </div>
        </div>
    </div>

    {{-- Konten Struk untuk print --}}
    {{-- Konten Struk untuk print --}}
    <div id="print_content" style="display:none;">
        <div class="receipt-card">
            <div class="receipt-header">
                <img src="{{ asset('aset/img/logo_struk.jpeg') }}" class="main-logo">
                <h3 class="receipt-title">PACIFIC POOL SUMBER</h3>
                <p class="receipt-address">Jl. Dewi Sartika No.8 Tukmudal, Sumber, Cirebon</p>
            </div>

            <div class="barcode-section">
                <img src="data:image/png;base64,{{ $barcode }}" id="barcodeImage">
                <p class="invoice-text">Invoice : {{ $purchase->invoice_no }}</p>
            </div>

            <hr class="receipt-divider">

            <div class="receipt-info">
                <div class="info-row">
                    <span class="info-label">Kasir</span>
                    <span class="info-value">: {{ $purchase->staff->username }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Transaksi</span>
                    <span class="info-value">: {{ \Carbon\Carbon::parse($purchase->created_at)->translatedFormat('d F Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pelanggan</span>
                    <span class="info-value">: {{ $purchase->customer->name ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Clubhouse</span>
                    <span class="info-value">{{ $purchase->customer->clubhouse->name ?? '-' }}</span>
                </div>
            </div>

            <hr class="receipt-divider">

            <ul class="purchase-detail">
                @foreach($purchase->purchaseDetails as $detail)
                    <li>
                        <span class="item-name">({{ $detail->qty }}x) {{ $detail->name }}</span>
                        <span class="item-price">{{ number_format($detail->price * $detail->qty, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>

            @if($purchase->promo)
                <div class="receipt-row">
                    <span>Diskon:</span>
                    <span>- Rp.{{ number_format($purchase->discount,0,',','.') }}</span>
                </div>
            @endif

            <div class="receipt-total">
                <span>Total</span>
                <span>Rp. {{ number_format($purchase->total,0,',','.') }}</span>
            </div>

            <div class="receipt-payment">
                <span>Uang Diterima</span>
                <span>Rp. {{ number_format($purchase->uangDiterima,0,',','.') }}</span>
            </div>

            <div class="receipt-payment">
                <span>Kembalian</span>
                <span>Rp. {{ number_format($purchase->kembalian,0,',','.') }}</span>
            </div>

            <div class="payment-method">
                @switch($purchase->payment)
                    @case(1) Tunai @break
                    @case(2) Qris BCA | {{ $purchase->approval_code }} @break
                    @case(3) Qris Mandiri | {{ $purchase->approval_code }} @break
                    @case(4) Debit BCA | {{ $purchase->approval_code }} @break
                    @case(5) Debit Mandiri | {{ $purchase->approval_code }} @break
                    @case(6) Transfer @break
                    @case(7) Qris BRI @break
                    @case(8) Debit BRI @break
                @endswitch
            </div>

            <p class="thank-you">Thank You For Coming</p>
        </div>

        <div class="receipt-card">
            <div class="receipt-header">
                <img src="{{ asset('aset/img/logo_struk.jpeg') }}" class="main-logo">
                <h3 class="receipt-title">PACIFIC POOL SUMBER</h3>
                <p class="receipt-address">Jl. Dewi Sartika No.8 Tukmudal, Sumber, Cirebon</p>
            </div>

            <div class="barcode-section">
                <img src="data:image/png;base64,{{ $barcode }}" id="barcodeImage">
                <p class="invoice-text">Invoice : {{ $purchase->invoice_no }}</p>
            </div>

            <hr class="receipt-divider">

            <div class="receipt-info">
                <div class="info-row">
                    <span class="info-label">Kasir</span>
                    <span class="info-value">: {{ $purchase->staff->username }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Transaksi</span>
                    <span class="info-value">: {{ \Carbon\Carbon::parse($purchase->created_at)->translatedFormat('d F Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pelanggan</span>
                    <span class="info-value">: {{ $purchase->customer->name ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Clubhouse</span>
                    <span class="info-value">{{ $purchase->customer->clubhouse->name ?? '-' }}</span>
                </div>
            </div>

            <hr class="receipt-divider">

            <ul class="purchase-detail">
                @foreach($purchase->purchaseDetails as $detail)
                    <li>
                        <span class="item-name">({{ $detail->qty }}x) {{ $detail->name }}</span>
                        <span class="item-price">{{ number_format($detail->price * $detail->qty, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>

            @if($purchase->promo)
                <div class="receipt-row">
                    <span>Diskon:</span>
                    <span>- Rp.{{ number_format($purchase->discount,0,',','.') }}</span>
                </div>
            @endif

            <div class="receipt-total">
                <span>Total</span>
                <span>Rp. {{ number_format($purchase->total,0,',','.') }}</span>
            </div>

            <div class="receipt-payment">
                <span>Uang Diterima</span>
                <span>Rp. {{ number_format($purchase->uangDiterima,0,',','.') }}</span>
            </div>

            <div class="receipt-payment">
                <span>Kembalian</span>
                <span>Rp. {{ number_format($purchase->kembalian,0,',','.') }}</span>
            </div>

            <div class="payment-method">
                @switch($purchase->payment)
                    @case(1) Tunai @break
                    @case(2) Qris BCA | {{ $purchase->approval_code }} @break
                    @case(3) Qris Mandiri | {{ $purchase->approval_code }} @break
                    @case(4) Debit BCA | {{ $purchase->approval_code }} @break
                    @case(5) Debit Mandiri | {{ $purchase->approval_code }} @break
                    @case(6) Transfer @break
                    @case(7) Qris BRI @break
                    @case(8) Debit BRI @break
                @endswitch
            </div>

            <p class="thank-you">Thank You For Coming</p>
        </div>
    </div>

    <script>
        function printReceipt() {
            const printContent = document.getElementById('print_content');
            printContent.style.display = 'block';
            window.print();
            printContent.style.display = 'none';
        }

        document.getElementById('print-ticket').addEventListener('click', function() {
            const printUrl = "{{ route('print_ticket', ['purchaseID' => $purchase->id]) }}";
            window.open(printUrl, '_blank');
        });
    </script>
@endsection