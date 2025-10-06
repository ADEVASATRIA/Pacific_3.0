@extends('front.admin.index')

@section('title', 'Data Package')
@section('page-title', 'Daftar Package Pacific')

@vite('resources/css/admin/package.css')
@stack('styles')

@section('top-controls')
    <form action="{{ route('admin.package') }}" method="get" class="package-search-form">
        <input type="text" name="phone" value="{{ $customerPhone ?? '' }}" placeholder="Masukkan nomor telepon..."
            required>
        <button type="submit">Cari Paket</button>
    </form>
@endsection

@section('content')
    <div class="package-container">
        @if ($customer)
            <div class="package-summary">
                <div class="summary-card">
                    <h4>Total Redeem (Termasuk Expired)</h4>
                    <p>{{ $totalRedeemedTickets }}</p>
                </div>
                <div class="summary-card">
                    <h4>Total Sisa Redeem (Aktif)</h4>
                    <p>{{ $totalQtyRedeemed }}</p>
                </div>
                <div class="summary-card">
                    <h4>Jumlah Paket Expired</h4>
                    <p>{{ $expiredDatesCount }}</p>
                </div>
            </div>

            <table class="package-table">
                <thead>
                    <tr>
                        <th>Tanggal Pembelian</th>
                        <th>Nomor Invoice</th>
                        <th>Nama Customer</th>
                        <th>Nomor Telepon</th>
                        <th>Total Jumlah Dibayar</th>
                        <th>Jenis Paket</th>
                        <th>Total Redeem</th>
                        <th>Sisa Qty Redeem</th>
                        <th>Tanggal Kedaluwarsa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        @foreach ($purchase->purchaseDetails as $detail)
                            @php
                                $redeem = $detail->packageComboRedeem ?? null;
                                $expiredDate = $redeem ? $redeem->expired_date : null;
                            @endphp
                            <tr>
                                <td>{{ $purchase->created_at ? $purchase->created_at->locale('id')->translatedFormat('d F Y') : '-' }}
                                </td>
                                <td>{{ $purchase->invoice_no ?? '-' }}</td>
                                <td>{{ $purchase->customer->name ?? '-' }}</td>
                                <td>{{ $purchase->customer->phone ?? '-' }}</td>
                                <td class="text-end">Rp {{ number_format($purchase->total ?? 0, 0, ',', '.') }}</td>
                                <td>{{ $redeem->name ?? '-' }}</td>
                                <td>{{ $redeem ? $redeem->details->sum('qty_printed') : 0 }}</td>
                                <td>{{ $redeem ? $redeem->details->sum('qty_redeemed') : 0 }}</td>
                                <td>
                                    {{ $expiredDate ? \Carbon\Carbon::parse($expiredDate)->locale('id')->translatedFormat('d F Y') : '-' }}
                                </td>
                            </tr>
                        @endforeach

                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Tidak ada data pembelian paket ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @else
            <div class="no-data">
                <p>Masukkan nomor telepon untuk melihat data paket pelanggan.</p>
            </div>
        @endif
    </div>
@endsection
