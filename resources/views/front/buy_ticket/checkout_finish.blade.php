@extends('main.blank')

@section('content')
    @vite('resources/css/front/checkout_finish.css')

    <div class="checkout-container">
        <div class="checkout-card">
            <h1 class="title">Terimakasih</h1>
            <p class="subtitle">
                Pembayaran sudah diterima, silahkan cetak struk dan tiket 
                (untuk Tiket Paket silahkan cetak tiket melalui tombol 
                <b>“Cetak Tiket”</b> di halaman depan)
            </p>

            <p class="refund">Total Kembalian = <span>Rp.0</span></p>

            <div class="button-group">
                <button class="btn primary" >Cetak Struk</button>
                <button class="btn success" id="print-ticket" >Cetak Tiket</button>
                <button class="btn secondary">Back To Home</button>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('print-ticket').addEventListener('click', function() {
            // Mengambil URL rute dari atribut 'data-print-url' (sebaiknya diletakkan di tombol)
            var printUrl = "{{ route('print_ticket', ['purchaseID' => $purchase->id]) }}";
    
            // Membuka URL di jendela/tab baru
            window.open(printUrl, '_blank');
        });
    </script>
@endsection
