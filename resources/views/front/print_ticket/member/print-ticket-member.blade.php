@extends('main.second_blank')
@section('content')
    @vite('resources/css/front/print-ticket-member.css')
    <div class="ticket-wrapper">
        <!-- Tombol Print -->
        <div class="print-btn" style="display: flex; gap: 10px; justify-content: center;">
            <button onclick="window.print()">Print Tiket</button>

            <button onclick="window.location.href='{{ route('main') }}'"
                style="background-color: #6c757d; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                Back to Home
            </button>
        </div>

        {{-- {{ dd($customer) }} --}}
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

        <!-- Kartu Tiket -->
        <div class="print_content">
            @if ($ticket)
                @foreach ($ticketEntries as $entry)
                    {{-- {{ dd($entry) }} --}}
                    <div class="ticket-card">
                        <!-- QR Code -->
                        <div class="qr-section mt-5">
                            {!! QrCode::size(180)->generate($entry->code) !!}
                        </div>

                        <!-- Detail Tiket -->
                        <div class="ticket-info">
                            <h2 class="ticket-title">{{ $ticket->code }}</h2>

                            {{-- Versi front office --}}
                            @if($entry->type == 1)
                                <p class="ticket-desc screen-only">
                                    {{ $ticket->purchaseDetail->ticketType->name ?? 'Tiket' }} <br>
                                    Berlaku Sampai Tanggal
                                    <strong>{{ \Carbon\Carbon::parse($ticket->date_end)->translatedFormat('d F Y') }}</strong>
                                </p>
                            @elseif($entry->type == 2)
                                <p class="ticket-desc screen-only">
                                    Tiket Pengantar Tambahan Gratis <br>
                                    Berlaku Sampai Tanggal
                                    <strong>{{ \Carbon\Carbon::parse($ticket->date_end)->translatedFormat('d F Y') }}</strong>
                                </p>
                            @endif


                            {{-- Versi print --}}
                            @if($entry->type == 1)
                                <p class="ticket-subtitle print-only">
                                    {{ $ticket->purchaseDetail->ticketType->name ?? 'Tiket' }} <br>
                                    {{ \Carbon\Carbon::parse($ticket->date_end)->translatedFormat('d F Y') }}
                                </p>
                            @elseif($entry->type == 2)
                                <p class="ticket-subtitle print-only">
                                    Tiket Pengantar Tambahan Gratis <br>
                                    {{ \Carbon\Carbon::parse($ticket->date_end)->translatedFormat('d F Y') }}
                                </p>
                            @endif
                            

                            <p class="ticket-price">
                                @if ($entry->type == 2)
                                    Gratis
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-center text-red-500">Tidak ada tiket member aktif.</p>
            @endif
        </div>
    </div>
@endsection
