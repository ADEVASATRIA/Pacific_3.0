@extends('front.admin.index')

@section('title', 'Data Transaksi')
@section('page-title', 'Daftar Transaksi')
@vite(['resources/css/admin/transaction.css', 'resources/css/front/checkout_finish.css'])


@section('top-controls')
    <form method="GET" action="{{ route('admin.transaksi') }}" class="filter-form">
        <div class="filter-wrapper">
            <!-- Payment Filter Section -->
            <div class="filter-section payment-section">
                <div class="filter-label">Filter Payment:</div>
                <div class="payment-options">
                    @foreach ($paymentOptions as $key => $label)
                        <label class="checkbox-chip">
                            <input type="checkbox" name="payment[]" value="{{ $key }}"
                                {{ in_array($key, $filterPayments ?? []) ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="chip-label">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Date Filter & Actions Section -->
            <div class="filter-section actions-section">
                <div class="date-filter-group">
                    <span class="filter-label">Filter Tanggal:</span>
                    <div class="date-inputs">
                        <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-d')) }}"
                            class="form-control date-input">
                        <span class="divider">s/d</span>
                        <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}"
                            class="form-control date-input">
                    </div>
                    <button type="submit" class="btn-filter">
                        Filter
                    </button>
                </div>

                <div class="export-group">
                    <button type="submit" name="export" value="1" class="btn-export">
                        Export Excel
                    </button>
                </div>
            </div>
        </div>
    </form>

@endsection

@section('content')
    <div class="cards">
        <div class="card">
            <h3>Kasir Aktif</h3>
            <p>{{ $staff->name ?? $staff->username }}</p>
        </div>
        <div class="card">
            <h3>Saldo Awal</h3>
            <p>Rp {{ number_format($saldoAwal, 0, ',', '.') }}</p>
        </div>
        <div class="card">
            <h3>Total Transaksi</h3>
            <p>{{ $totalTransaksi }}</p>
        </div>
        <div class="card">
            <h3>Pendapatan Hari Ini</h3>
            <p>Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
        </div>
        <div class="card">
            <h3>Pendapatan + Saldo Awal</h3>
            <p>Rp {{ number_format($pendapatanDenganSaldo, 0, ',', '.') }}</p>
        </div>
        <div class="card">
            <h3>Menunggu</h3>
            <p>{{ $pending }}</p>
        </div>
    </div>


    <section class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Tanggal</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td><strong>{{ $tx->id }}</strong></td>
                        <td>{{ $tx->customer?->name ?? '-' }}</td>
                        <td>{{ $tx->customer?->phone ?? '-' }}</td>
                        <td class="small">{{ \Carbon\Carbon::parse($tx->created_at)->format('d M Y | H:i') }}</td>
                        <td>
                            <ul>
                                @foreach ($tx->purchaseDetails as $detail)
                                    <li>{{ $detail->name }} (x{{ $detail->qty }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>Rp {{ number_format($tx->total, 0, ',', '.') }}</td>
                        <td>{{ $tx->payment_label }}</td>
                        <td>
                            @if ($tx->status == 0)
                                <span class="status new">Baru</span>
                            @elseif($tx->status == 1)
                                <span class="status pending">Pending</span>
                            @elseif($tx->status == 2)
                                <span class="status paid">Paid</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn-print receipt" href="{{ route('admin.print_receipt', ['id' => $tx->id]) }}">
                                    Print Struk
                                </a>
                                <a class="btn-print ticket" href="{{ route('print_ticket', ['purchaseID' => $tx->id]) }}">
                                    Print Tiket
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="small">Belum ada transaksi hari ini</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
