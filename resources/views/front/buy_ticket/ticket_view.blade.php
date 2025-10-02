@extends('main.blank')
@section('content')
    @vite('resources/css/front/index_ticket.css')

    <div class="ticket-wrapper">
        <div class="ticket-card">
            {{-- Customer Info --}}
            @if ($customer)
                <div class="customer-info">
                    <h3>Informasi Customer</h3>
                    <p><strong>Nama:</strong> {{ $customer->name }}</p>
                    <p><strong>Telepon:</strong> {{ $customer->phone ?? '-' }}</p>
                </div>
            @endif

            <h2 class="ticket-title">Beli Tiket</h2>
            <p class="ticket-subtitle">Masukkan Jumlah Tiket yang akan di beli</p>

            {{-- Filter Tipe Tiket --}}
            <div class="ticket-filter">
                <form method="GET" action="{{ route('index_ticket') }}">
                    <input type="hidden" name="customer" value="{{ $customerId ?? '' }}">
                    <div class="radio-group">
                        <input type="radio" id="all" name="filter_type" value="" onchange="this.form.submit()"
                            {{ !$filterType ? 'checked' : '' }}>
                        <label for="all">Semua Tipe</label>

                        <input type="radio" id="regular" name="filter_type" value="1" onchange="this.form.submit()"
                            {{ $filterType == 1 ? 'checked' : '' }}>
                        <label for="regular">Regular</label>

                        <input type="radio" id="tiketPengantar" name="filter_type" value="2" onchange="this.form.submit()"
                            {{ $filterType == 2 ? 'checked' : '' }}>
                        <label for="tiketPengantar">Tiket Pengantar</label>

                        <input type="radio" id="tiketPelatih" name="filter_type" value="3" onchange="this.form.submit()"
                            {{ $filterType == 3 ? 'checked' : '' }}>
                        <label for="tiketPelatih">Tiket Pelatih</label>
                        
                        <input type="radio" id="tiketMember" name="filter_type" value="4" onchange="this.form.submit()"
                            {{ $filterType == 4 ? 'checked' : '' }}>
                        <label for="tiketMember">Tiket Member</label>
                        
                        <input type="radio" id="tiketPackage" name="filter_type" value="5" onchange="this.form.submit()"
                            {{ $filterType == 5 ? 'checked' : '' }}>
                        <label for="tiketPackage">Tiket Package</label>
                    </div>
                </form>
            </div>

            <form action="{{ route('submit_form_ticket') }}" method="POST" class="ticket-form">
            {{-- <form action="" method="POST" class="ticket-form"> --}}
                @csrf

                {{-- Tiket Regular --}}
                @if ($ticketRegular->count())
                    <div class="ticket-section">
                        <h3 class="section-title">Tiket 1x Pakai</h3>
                        <div class="ticket-grid">
                            @foreach ($ticketRegular as $ticket)
                                <div class="ticket-item">
                                    <h4>{{ $ticket->name }}
                                        @if ($ticket->description)
                                            <span>({{ $ticket->description }})</span>
                                        @endif
                                    </h4>
                                    <p class="price">Rp {{ number_format($ticket->price, 0, ',', '.') }}</p>
                                    <div class="qty-control">
                                        <button type="button" class="btn-minus"
                                            data-target="qty-{{ $ticket->id }}">-</button>
                                        <input type="number" name="tickets[{{ $ticket->id }}][qty]"
                                            id="qty-{{ $ticket->id }}" value="0" min="0" class="qty-input">
                                        <button type="button" class="btn-plus"
                                            data-target="qty-{{ $ticket->id }}">+</button>
                                    </div>
                                </div>

                                {{-- hidden meta --}}
                                <input type="hidden" name="tickets[{{ $ticket->id }}][id]" value="{{ $ticket->id }}">
                                <input type="hidden" name="tickets[{{ $ticket->id }}][name]"
                                    value="{{ $ticket->name }}">
                                <input type="hidden" name="tickets[{{ $ticket->id }}][price]"
                                    value="{{ $ticket->price }}">
                                <input type="hidden" name="tickets[{{ $ticket->id }}][type_purchase]" value="1">
                                <input type="hidden" name="customer_id" value="{{ $customerId ?? '' }}">
                            @endforeach
                        </div>
                    </div>
                @endif
                
                {{-- Tiket Pengantar --}}
                @if ($ticketPengantar->count())
                    <div class="ticket-section">
                        <h3 class="section-title">Tiket Pengantar</h3>
                        <div class="ticket-grid">
                            @foreach ($ticketPengantar as $ticket)
                                <div class="ticket-item">
                                    <h4>{{ $ticket->name }}</h4>
                                    <p class="price">Rp {{ number_format($ticket->price, 0, ',', '.') }}</p>
                                    <div class="qty-control">
                                        <button type="button" class="btn-minus"
                                            data-target="qty-{{ $ticket->id }}">-</button>
                                        <input type="number" name="tickets[{{ $ticket->id }}][qty]"
                                            id="qty-{{ $ticket->id }}" value="0" min="0" class="qty-input">
                                        <button type="button" class="btn-plus"
                                            data-target="qty-{{ $ticket->id }}">+</button>
                                    </div>
                                </div>

                                <input type="hidden" name="tickets[{{ $ticket->id }}][id]" value="{{ $ticket->id }}">
                                <input type="hidden" name="tickets[{{ $ticket->id }}][name]"
                                    value="{{ $ticket->name }}">
                                <input type="hidden" name="tickets[{{ $ticket->id }}][price]"
                                    value="{{ $ticket->price }}">
                                <input type="hidden" name="tickets[{{ $ticket->id }}][type_purchase]" value="1">
                                <input type="hidden" name="customer_id" value="{{ $customerId ?? '' }}">
                            @endforeach
                        </div>
                    </div>
                @endif
                
                {{-- Tiket Pelatih --}}
                @if ($ticketPelatih->count())
                    <div class="ticket-section">
                        <h3 class="section-title">Tiket Pengantar</h3>
                        <div class="ticket-grid">
                            @foreach ($ticketPelatih as $ticket)
                                <div class="ticket-item">
                                    <h4>{{ $ticket->name }}</h4>
                                    <p class="price">Rp {{ number_format($ticket->price, 0, ',', '.') }}</p>
                                    <div class="qty-control">
                                        <button type="button" class="btn-minus"
                                            data-target="qty-{{ $ticket->id }}">-</button>
                                        <input type="number" name="tickets[{{ $ticket->id }}][qty]"
                                            id="qty-{{ $ticket->id }}" value="0" min="0" class="qty-input">
                                        <button type="button" class="btn-plus"
                                            data-target="qty-{{ $ticket->id }}">+</button>
                                    </div>
                                </div>

                                <input type="hidden" name="tickets[{{ $ticket->id }}][id]" value="{{ $ticket->id }}">
                                <input type="hidden" name="tickets[{{ $ticket->id }}][name]"
                                    value="{{ $ticket->name }}">
                                <input type="hidden" name="tickets[{{ $ticket->id }}][price]"
                                    value="{{ $ticket->price }}">
                                <input type="hidden" name="tickets[{{ $ticket->id }}][type_purchase]" value="1">
                                <input type="hidden" name="customer_id" value="{{ $customerId ?? '' }}">
                            @endforeach
                        </div>
                    </div>
                @endif
                
                {{-- Member --}}
                @if ($ticketMember->count())
                    <div class="ticket-section">
                        <h3 class="section-title">Member</h3>
                        <div class="ticket-grid">
                            @foreach ($ticketMember as $ticket)
                                <div class="ticket-item">
                                    <h4>{{ $ticket->name }}</h4>
                                    <p class="price">Rp {{ number_format($ticket->price, 0, ',', '.') }}</p>
                                    <div class="qty-control">
                                        <button type="button" class="btn-minus"
                                            data-target="qty-{{ $ticket->id }}">-</button>
                                        <input type="number" name="tickets[{{ $ticket->id }}][qty]"
                                            id="qty-{{ $ticket->id }}" value="0" min="0" class="qty-input">
                                        <button type="button" class="btn-plus"
                                            data-target="qty-{{ $ticket->id }}">+</button>
                                    </div>
                                </div>

                                <input type="hidden" name="tickets[{{ $ticket->id }}][id]" value="{{ $ticket->id }}">
                                <input type="hidden" name="tickets[{{ $ticket->id }}][name]"
                                    value="{{ $ticket->name }}">
                                <input type="hidden" name="tickets[{{ $ticket->id }}][price]"
                                    value="{{ $ticket->price }}">
                                <input type="hidden" name="tickets[{{ $ticket->id }}][type_purchase]" value="1">
                                <input type="hidden" name="customer_id" value="{{ $customerId ?? '' }}">
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Paket --}}
                @if ($ticketPackage->count())
                    <div class="ticket-section">
                        <h3 class="section-title">Paket</h3>
                        <div class="ticket-grid">
                            @foreach ($ticketPackage as $pack)
                                <div class="ticket-item">
                                    <h4>{{ $pack->name }}</h4>
                                    <p class="price">Rp {{ number_format($pack->price, 0, ',', '.') }}</p>
                                    <div class="qty-control">
                                        <button type="button" class="btn-minus"
                                            data-target="qty-pack-{{ $pack->id }}">-</button>
                                        <input type="number" name="packages[{{ $pack->id }}][qty]"
                                            id="qty-pack-{{ $pack->id }}" value="0" min="0"
                                            class="qty-input">
                                        <button type="button" class="btn-plus"
                                            data-target="qty-pack-{{ $pack->id }}">+</button>
                                    </div>
                                </div>

                                <input type="hidden" name="packages[{{ $pack->id }}][id]"
                                    value="{{ $pack->id }}">
                                <input type="hidden" name="packages[{{ $pack->id }}][name]"
                                    value="{{ $pack->name }}">
                                <input type="hidden" name="packages[{{ $pack->id }}][price]"
                                    value="{{ $pack->price }}">
                                <input type="hidden" name="packages[{{ $pack->id }}][type_purchase]"
                                    value="3">
                                <input type="hidden" name="customer_id" value="{{ $customerId ?? '' }}">
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="form-submit">
                    <button type="submit" id="ticketSubmit" class="btn-submit">Submit</button>
                </div>

            </form>
        </div>
    </div>

    {{-- Konfirmasi Modal --}}
    <div id="confirmModal" class="modal hidden">
        <div class="modal-content">
            <h3>Konfirmasi Pembelian</h3>
            <div class="modal-body">
                <p><strong>Nama:</strong> <span id="confirmName">{{ $customer->name ?? '-' }}</span></p>
                <p><strong>Telepon:</strong> <span id="confirmPhone">{{ $customer->phone ?? '-' }}</span></p>
                <p><strong>Tanggal:</strong> <span id="confirmDate"></span></p>
                <h4>Item yang dibeli:</h4>
                <ul id="confirmItems"></ul>
                <p class="total"><strong>Total:</strong> Rp <span id="confirmTotal">0</span></p>
            </div>
            <div class="modal-actions">
                <button type="button" id="btnCancel" class="btn-cancel">Batal</button>
                <button type="button" id="btnConfirm" class="btn-confirm">Konfirmasi</button>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function showAlert(type, message) {
        const existing = document.querySelector('.alert-slide');
        if (existing) existing.remove();

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert-slide ${type}`;
        alertDiv.innerHTML = `
            <div style="font-weight:600; margin-right:.4rem;">${type === 'error' ? 'Gagal!' : 'Berhasil!'}</div>
            <div style="flex:1;">${message}</div>
            <button class="alert-close" aria-label="close">&times;</button>
        `;
        document.body.appendChild(alertDiv);

        alertDiv.querySelector('.alert-close').addEventListener('click', () => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 250);
        });

        // show
        setTimeout(() => alertDiv.classList.add('show'), 50);

        // auto hide
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 300);
        }, 4000);
    }

    @if(session('error'))
        showAlert('error', @json(session('error')));
    @endif

    @if(session('success'))
        showAlert('success', @json(session('success')));
    @endif
});
</script>
@endsection

