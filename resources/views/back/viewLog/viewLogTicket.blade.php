@extends('main.back_blank')
@section('title', 'Log History Ticket')

<style>
    /* Summary Cards */
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    .summary-card h4 {
        font-size: 13px;
        color: #6b7280;
        margin: 0 0 8px 0;
        font-weight: 500;
    }

    .summary-card .value {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
    }

    /* Tab Menu */
    .history-tabs {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0 0 20px 0;
        gap: 8px;
        border-bottom: 2px solid #e5e7eb;
    }

    .history-tabs li {
        padding: 12px 20px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #6b7280;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s;
    }

    .history-tabs li:hover {
        color: #3b82f6;
    }

    .history-tabs li.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
    }

    /* Tab Content */
    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }

    /* Table */
    .log-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .log-table thead {
        background: #f8fafc;
    }

    .log-table th {
        padding: 14px 16px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }

    .log-table td {
        padding: 14px 16px;
        font-size: 14px;
        color: #4b5563;
        border-bottom: 1px solid #f3f4f6;
    }

    .log-table tbody tr:hover {
        background: #f9fafb;
    }

    .log-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Alert Info */
    .alert-info {
        background: #eff6ff;
        color: #1e40af;
        padding: 16px 20px;
        border-radius: 10px;
        font-size: 14px;
        text-align: center;
        border: 1px solid #bfdbfe;
    }

    /* Selected Date Display */
    .selected-date-info {
        background: #f0fdf4;
        color: #166534;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 16px;
        border: 1px solid #bbf7d0;
    }
</style>

@section('content')
    <div class="ticket-types-page">
        <h2 class="page-title">Log History Ticket</h2>
        <div class="filter-section mb-4">
            {{-- Filter Section --}}
            <form method="GET" action="{{ route('view-log-history-ticket') }}" class="filter-form flex items-end gap-4 flex-wrap">
                <div class="form-group">
                    <label for="date" class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" name="date" id="date" value="{{ $selectedDate }}" class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="form-group">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                    <input type="text" name="phone" id="phone" placeholder="Cari nomor telepon..."
                        value="{{ $phone ?? '' }}" class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="form-group">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700">
                        <i data-feather="search" style="width: 16px; height: 25px; margin-right: 4px;"></i>
                        Cari
                    </button>
                </div>
            </form>
        </div>

        {{-- Selected Date Info --}}
        <div class="selected-date-info">
            Menampilkan data untuk tanggal:
            <strong>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</strong>
            @if($phone)
                | Filter telepon: <strong>{{ $phone }}</strong>
            @endif
        </div>

        {{-- Summary Cards --}}
        <div class="summary-cards">
            <div class="summary-card">
                <h4>Total Tiket Package</h4>
                <div class="value">{{ $todaysSummary['total_package_tickets'] }}</div>
            </div>
            <div class="summary-card">
                <h4>Total Tiket Single</h4>
                <div class="value">{{ $todaysSummary['total_single_tickets'] }}</div>
            </div>
            <div class="summary-card">
                <h4>Total Tiket Member</h4>
                <div class="value">{{ $todaysSummary['total_member_tickets'] }}</div>
            </div>
            <div class="summary-card">
                <h4>Total Tiket Pelatih</h4>
                <div class="value">{{ $todaysSummary['total_trainer_tickets'] }}</div>
            </div>
            <div class="summary-card">
                <h4>Total Customer Unik</h4>
                <div class="value">{{ $todaysSummary['unique_customers'] }}</div>
            </div>
        </div>

        {{-- Tab Menu --}}
        <ul class="history-tabs" id="tabs">
            <li class="active" data-tab="package">Tiket Package</li>
            <li data-tab="single">Tiket Single</li>
            <li data-tab="member">Tiket Member</li>
            <li data-tab="trainer">Tiket Pelatih</li>
        </ul>

        {{-- Tab Content --}}
        <div class="tabs-content">

            {{-- PACKAGE TABLE --}}
            <section class="tab-pane active" data-content="package">
                @if ($logQtyPacket->count() > 0)
                    <table class="log-table">
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
                                    <td>{{ $ticket->package_combo_redeem->name ?? '-' }}</td>
                                    <td>{{ $ticket->created_at->format('H:i:s') }}</td>
                                    <td>{{ $ticket->package_combo_redeem?->created_at?->format('d M Y') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert-info">Tidak ada tiket package yang di-redeem pada tanggal ini.</div>
                @endif
            </section>

            {{-- SINGLE TABLE --}}
            <section class="tab-pane" data-content="single">
                @if ($logPrintSingles->count() > 0)
                    <table class="log-table">
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
                                    <td>{{ $ticket->name_tickets ?? '-' }}</td>
                                    <td>{{ $ticket->created_at->format('d M Y H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert-info">Tidak ada tiket single yang di-print pada tanggal ini.</div>
                @endif
            </section>

            {{-- MEMBER TABLE --}}
            <section class="tab-pane" data-content="member">
                @if ($logPrintMember->count() > 0)
                    <table class="log-table">
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
                    <div class="alert-info">Tidak ada tiket member yang di-print pada tanggal ini.</div>
                @endif
            </section>

            {{-- TRAINER TABLE --}}
            <section class="tab-pane" data-content="trainer">
                @if ($logPrintPelatih->count() > 0)
                    <table class="log-table">
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
                    <div class="alert-info">Tidak ada tiket pelatih yang di-print pada tanggal ini.</div>
                @endif
            </section>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tabList = document.getElementById('tabs');
            const tabButtons = tabList ? tabList.querySelectorAll('li[data-tab]') : [];
            const tabContents = document.querySelectorAll('.tab-pane[data-content]');

            if (tabButtons.length === 0 || tabContents.length === 0) {
                console.error('Tab elements not found!');
                return;
            }

            function switchTab(targetTab) {
                // Remove active from all tabs
                tabButtons.forEach(function (tab) {
                    tab.classList.remove('active');
                });

                // Hide all content
                tabContents.forEach(function (content) {
                    content.classList.remove('active');
                });

                // Activate target tab
                const activeTabButton = tabList.querySelector(`li[data-tab="${targetTab}"]`);
                if (activeTabButton) {
                    activeTabButton.classList.add('active');
                }

                // Show target content
                const activeContent = document.querySelector(`.tab-pane[data-content="${targetTab}"]`);
                if (activeContent) {
                    activeContent.classList.add('active');
                }
            }

            // Attach click event to each tab
            tabButtons.forEach(function (tabButton) {
                tabButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const targetTab = this.getAttribute('data-tab');
                    if (targetTab) {
                        switchTab(targetTab);
                    }
                });
            });
        });
    </script>
@endsection