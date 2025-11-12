@extends('main.blank')

@section('content')
    @vite('resources/css/front/print_ticket.css')
    <div class="notice-info">
        <p>
            <strong>Catatan:</strong> Tiket paket <u>tidak dapat dicetak</u> di halaman ini.
            Silakan cetak tiket paket melalui tombol <b>"Cetak Tiket"</b> di halaman utama.
        </p>
    </div>

    <div class="ticket-wrapper">
        <!-- Tombol Aksi -->
        <div class="print-btn" style="display: flex; gap: 10px; justify-content: center;">
            <button onclick="window.print()">Print Tiket</button>

            <button onclick="window.location.href='{{ route('main') }}'"
                style="background-color: #6c757d; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                Back to Home
            </button>
        </div>

        <!-- Informasi Customer -->
        <div class="ticket-card customer-info">
            <h3 class="section-title">Informasi Customer</h3>
            <div class="info-row">
                <span>Nama Customer</span>
                <span>{{ $customer->name }}</span>
            </div>
            <div class="info-row">
                <span>Nama Club House</span>
                <span>{{ $customer->clubhouse->name ?? ($customer->clubhouse2->name ?? 'Tidak Ada') }}</span>
            </div>
        </div>

        <!-- Daftar Tiket -->
        <div class="print_content">
            @foreach ($tickets as $ticket)
                @php
                    $entries = $ticketEntries->where('ticket_id', $ticket->id);
                    $purchaseDetail = $purchaseDetails->where('id', $ticket->purchase_detail_id)->first();
                @endphp

                @foreach ($entries as $entry)
                    <div class="ticket-card">
                        <!-- QR Code -->
                        <div class="qr-section">
                            {!! QrCode::size(120)->generate($entry->code) !!}
                        </div>

                        <!-- Detail Tiket -->
                        <div class="ticket-info">
                            <h2 class="ticket-title">{{ $ticket->code }}</h2>

                            {{-- Versi front office --}}
                            <p class="ticket-desc screen-only">
                                @if ($entry->type == 1)
                                    {{ $ticket->purchaseDetail->ticketType->name ?? 'Tiket' }} <br>
                                    Berlaku Sampai Tanggal
                                    <strong>{{ \Carbon\Carbon::parse($ticket->date_end)->translatedFormat('d F Y') }}</strong>
                                @elseif($entry->type == 2)
                                    Tiket Pengantar Tambahan Gratis <br>
                                    Berlaku Sampai Tanggal
                                    <strong>{{ \Carbon\Carbon::parse($ticket->date_end)->translatedFormat('d F Y') }}</strong>
                                @endif
                            </p>

                            {{-- Versi print --}}
                            <p class="ticket-subtitle print-only">
                                @if ($entry->type == 1)
                                    {{ $ticket->purchaseDetail->ticketType->name ?? 'Tiket' }} -
                                    {{ \Carbon\Carbon::parse($ticket->date_end)->translatedFormat('d F Y') }}
                                @elseif($entry->type == 2)
                                    Tiket Pengantar Tambahan Gratis <br>
                                    {{ \Carbon\Carbon::parse($ticket->date_end)->translatedFormat('d F Y') }}
                                @endif
                            </p>

                            <p class="ticket-price">
                                @if ($entry->type == 2)
                                    Gratis
                                @else
                                    Rp {{ number_format($purchaseDetail->price ?? 0, 0, ',', '.') }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
@endsection
