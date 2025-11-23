@extends('front.admin.index')

@section('title', 'Data History Tiket Hari ini')
@section('page-title', 'Data History Tiket Hari ini')

@vite('resources/css/admin/viewHistoryTickets.css')

@section('top-controls')
    <form method="GET" action="{{ route('admin.viewHistoryTickets') }}" class="filter-form">
        <div class="filter-box">
            <label for="phone"><strong>Cari Nomor Telepon:</strong></label>
            <input type="text" name="phone" id="phone" class="form-control" placeholder="Masukkan nomor telepon"
                value="{{ request('phone') }}">

            <button type="submit" class="btn btn-primary">Cari</button>
        </div>
    </form>
@endsection


@section('content')

    {{-- Summary Cards --}}
    <div class="cards">
        <div class="card">
            <h3>Total Tiket Package</h3>
            <p>{{ $todaysSummary['total_package_tickets'] }}</p>
        </div>
        <div class="card">
            <h3>Total Tiket Single</h3>
            <p>{{ $todaysSummary['total_single_tickets'] }}</p>
        </div>
        <div class="card">
            <h3>Total Tiket Member</h3>
            <p>{{ $todaysSummary['total_member_tickets'] }}</p>
        </div>
        <div class="card">
            <h3>Total Customer</h3>
            <p>{{ $todaysSummary['unique_customers'] }}</p>
        </div>
    </div>


    {{-- TAB MENU --}}
    <ul class="history-tabs" id="tabs">
        <li class="active" data-tab="package">Tiket Package</li>
        <li data-tab="single">Tiket Single</li>
        <li data-tab="member">Tiket Member</li>
        <li data-tab="trainer">Tiket Pelatih</li>
    </ul>



    {{-- TAB CONTENT --}}
    <div class="tabs-content">

        {{-- PACKAGE TABLE --}}
        <section class="tab-pane active" data-content="package">
            @if ($logQtyPacket->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Customer</th>
                            <th>No. Telepon</th>
                            <th>Nama Package</th>
                            <th>Waktu Redeem</th>
                            <th>Tanggal Pembelian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logQtyPacket as $index => $ticket)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $ticket->log_redeem_packet_tickets->customer_name ?? '-' }}</td>
                                <td>{{ $ticket->log_redeem_packet_tickets->phone ?? '-' }}</td>
                                <td>{{ $ticket->package_combo_redeem->name }}</td>
                                <td>{{ $ticket->created_at->format('H:i:s') }}</td>
                                <td>{{ $ticket->package_combo_redeem->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert-info">Tidak ada tiket package yang di-redeem hari ini.</div>
            @endif
        </section>


        {{-- SINGLE TABLE --}}
        <section class="tab-pane" data-content="single">
            @if ($logPrintSingles->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Customer</th>
                            <th>No. Telepon</th>
                            <th>Kode Tiket</th>
                            <th>Nama Tiket</th>
                            <th>Waktu Print</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logPrintSingles as $index => $ticket)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $ticket->customer_name ?? '-' }}</td>
                                <td>{{ $ticket->phone ?? '-' }}</td>
                                <td>{{ $ticket->ticket_code }}</td>
                                <td>{{ $ticket->name_tickets }}</td>
                                <td>{{ $ticket->created_at->format('d M Y H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert-info">Tidak ada tiket single yang di-print hari ini.</div>
            @endif
        </section>


        {{-- MEMBER TABLE --}}
        <section class="tab-pane" data-content="member">
            @if ($logPrintMember->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Customer</th>
                            <th>No. Telepon</th>
                            <th>Kode Tiket</th>
                            <th>Waktu Print</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logPrintMember as $index => $ticket)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $ticket->customer_name ?? '-' }}</td>
                                <td>{{ $ticket->phone ?? '-' }}</td>
                                <td>{{ $ticket->ticket_code }}</td>
                                <td>{{ $ticket->created_at->format('d M Y H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert-info">Tidak ada tiket member yang di-print hari ini.</div>
            @endif
        </section>


        {{-- TRAINER TABLE --}}
        <section class="tab-pane" data-content="trainer">
            @if ($logPrintPelatih->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Customer</th>
                            <th>No. Telepon</th>
                            <th>Kode Tiket</th>
                            <th>Waktu Print</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logPrintPelatih as $index => $ticket)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $ticket->customer_name ?? '-' }}</td>
                                <td>{{ $ticket->phone ?? '-' }}</td>
                                <td>{{ $ticket->ticket_code }}</td>
                                <td>{{ $ticket->created_at->format('d M Y H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert-info">Tidak ada tiket pelatih yang di-print hari ini.</div>
            @endif
        </section>

    </div>


@endsection


@push('scripts')
    <script>
        console.log('🚀 Admin View History Tickets Script Loaded');
        (function() {
            'use strict';

            console.log('🔧 Tab Switcher Initialized');

            // Tunggu DOM selesai dimuat
            document.addEventListener("DOMContentLoaded", function() {

                // Ambil semua tab dan content dengan selector yang lebih spesifik
                const tabList = document.getElementById('tabs');
                const tabButtons = tabList ? tabList.querySelectorAll('li[data-tab]') : [];
                const tabContents = document.querySelectorAll('.tab-pane[data-content]');

                console.log('📊 Found tabs:', tabButtons.length);
                console.log('📄 Found contents:', tabContents.length);

                // Validasi elemen ditemukan
                if (tabButtons.length === 0 || tabContents.length === 0) {
                    console.error('❌ Tab elements not found!');
                    return;
                }

                // Fungsi untuk switch tab
                function switchTab(targetTab) {
                    console.log('🔄 Switching to tab:', targetTab);

                    // 1. Remove active dari semua tabs
                    tabButtons.forEach(function(tab) {
                        tab.classList.remove('active');
                    });

                    // 2. Hide semua content
                    tabContents.forEach(function(content) {
                        content.classList.remove('active');
                    });

                    // 3. Activate target tab
                    const activeTabButton = tabList.querySelector(`li[data-tab="${targetTab}"]`);
                    if (activeTabButton) {
                        activeTabButton.classList.add('active');
                    }

                    // 4. Show target content
                    const activeContent = document.querySelector(`.tab-pane[data-content="${targetTab}"]`);
                    if (activeContent) {
                        activeContent.classList.add('active');
                        console.log('✅ Tab switched successfully');
                    } else {
                        console.error('❌ Content not found for:', targetTab);
                    }
                }

                // Attach click event ke setiap tab
                tabButtons.forEach(function(tabButton) {
                    tabButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const targetTab = this.getAttribute('data-tab');
                        console.log('👆 Tab clicked:', targetTab);

                        if (targetTab) {
                            switchTab(targetTab);
                        }
                    });

                    // Tambahkan visual feedback saat hover
                    tabButton.addEventListener('mouseenter', function() {
                        this.style.opacity = '0.8';
                    });

                    tabButton.addEventListener('mouseleave', function() {
                        this.style.opacity = '1';
                    });
                });

                console.log('✅ Tab Switcher Ready');
            });
        })();
    </script>
    
@endpush
