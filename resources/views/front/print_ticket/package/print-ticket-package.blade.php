@extends('main.blank')

@section('content')
    @vite('resources/css/front/print-ticket-package.css')

    <div class="ticket-wrapper">
        {{-- Tombol print hanya untuk web --}}
        <div class="print-btn" style="display: flex; gap: 10px; justify-content: center;">
            <button onclick="window.print()">Print Tiket</button>

            <button onclick="window.location.href='{{ route('main') }}'"
                style="background-color: #6c757d; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                Back to Home
            </button>
        </div>


        {{-- Customer Info hanya untuk web --}}
        <div class="ticket-card customer-info screen-only mb-3">
            <h3 class="section-title">Informasi Customer</h3>
            <div class="info-row"><span>Nama Customer</span><span>{{ $customer->name }}</span></div>
            <div class="info-row"><span>No Telepon</span><span>{{ $customer->phone }}</span></div>
            <div class="info-row"><span>Tiket Redeem Hari Ini</span><span>{{ $redeemCount }} Tiket</span></div>

            <hr class="divider">

            <h3 class="section-title">Status Redeem Paket</h3>
            <div class="info-row"><span>Tiket Sudah Di Redeem</span><span>{{ $totalPrinted }} Tiket</span></div>
            <div class="info-row"><span>Sisa Tiket</span><span>{{ $totalRemaining }} Tiket</span></div>
            <div class="info-row"><span>Tanggal
                    Kedaluwarsa</span><span>{{ \Carbon\Carbon::parse($expiredDate)->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        {{-- Konten tiket untuk web & print --}}
        <div class="print_content mb-3">
            @foreach ($tickets as $ticket)
                @foreach ($ticketEntries->where('ticket_id', $ticket->id) as $entry)
                    <div class="ticket-card mb-3">
                        <div class="qr-section">
                            {!! QrCode::size(120)->generate($entry->code) !!}
                        </div>

                        <div class="ticket-info">
                            @if ($entry->type == 1)
                                <h2 class="ticket-title">{{ $ticket->packageComboRedeemDetail->name ?? 'Tiket' }}</h2>
                            @elseif($entry->type == 2)
                                <h2 class="ticket-title">Tiket Pengantar Tambahan Gratis</h2>
                            @endif
                            <p class="ticket-desc screen-only">
                                Berlaku sampai
                                <strong>{{ \Carbon\Carbon::parse($ticket->date_end)->translatedFormat('d F Y') }}</strong>
                            </p>
                            <p class="ticket-subtitle print-only">
                                Berlaku sampai {{ \Carbon\Carbon::parse($ticket->date_end)->translatedFormat('d F Y') }}
                            </p>

                            <p class="ticket-price">
                                @if ($entry->type == 2)
                                    Gratis
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
@endsection
