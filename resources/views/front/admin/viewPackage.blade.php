@extends('front.admin.index')

@section('title', 'Data Package')
@section('page-title', 'Daftar Package Pacific')

@vite('resources/css/admin/package.css')
@stack('styles')

@section('top-controls')
    <div class="top-controls-wrapper">
        <form action="{{ route('admin.package') }}" method="get" class="package-search-form">
            <input type="text" name="phone" value="{{ $customerPhone ?? '' }}" placeholder="Masukkan nomor telepon..." required>
            <button type="submit" class="btn-search">
                <i data-lucide="search"></i>
                Cari Paket
            </button>
        </form>

        @if (!empty($customer))
            <button id="btnHistoryRedeem" class="btn-history" style="margin-top: -14px;">
                <i data-lucide="book"></i>
                History Redeem
            </button>
        @endif
    </div>
@endsection

@section('content')
    <div class="package-container">
        @if ($customer)
            <div class="package-summary">
                <div class="summary-card">
                    <h4>Total Redeem (Termasuk Expired)</h4>
                    <p>{{ $totalRedeemedTickets ?? 0 }}</p>
                </div>
                <div class="summary-card">
                    <h4>Total Sisa Redeem (Aktif)</h4>
                    <p>{{ $totalQtyRedeemed ?? 0 }}</p>
                </div>
                <div class="summary-card">
                    <h4>Jumlah Paket Expired</h4>
                    <p>{{ $expiredDatesCount ?? 0 }}</p>
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
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases ?? [] as $purchase)
                        @foreach ($purchase->purchaseDetails as $detail)
                            @php
                                $redeem = $detail->packageComboRedeem;
                            @endphp
                            <tr>
                                <td>{{ $purchase->created_at?->locale('id')->translatedFormat('d F Y') }}</td>
                                <td>{{ $purchase->invoice_no }}</td>
                                <td>{{ $purchase->customer->name }}</td>
                                <td>{{ $purchase->customer->phone }}</td>
                                <td class="text-end">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                                <td>{{ $redeem->name }}</td>
                                <td>{{ $redeem->details->sum('qty_printed') }}</td>
                                <td>{{ $redeem->details->sum('qty_redeemed') }}</td>
                                <td>{{ \Carbon\Carbon::parse($redeem->expired_date)->locale('id')->translatedFormat('d F Y') }}</td>
                                <td>
                                    <button class="btn-history btnDetailHistory" data-id="{{ $redeem->id }}">
                                        <i data-lucide="book"></i>
                                        History Redeem
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">Tidak ada data pembelian paket ditemukan.</td>
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


{{-- ========================== MODAL LIST REDEEM ========================== --}}
<div id="logHistoryRedeemModal" class="modal">
    <div class="modal-content">
        <h2>Log History Redeem</h2>
        <div style="overflow-x: auto;">
            <table class="log-table">
                <thead>
                    <tr>
                        <th>Tanggal Pembelian</th>
                        <th>Nama Paket</th>
                        <th>Tanggal Cetak</th>
                    </tr>
                </thead>
                <tbody id="logHistoryBody">
                    <tr><td colspan="3" class="text-center text-muted">Belum ada data</td></tr>
                </tbody>
            </table>
        </div>
        <button id="closeModalRedeem" class="btn-secondary mt-4">Tutup</button>
    </div>
</div>


{{-- ====================== MODAL DETAIL REDEEM PER PACKAGE ====================== --}}
<div id="logHistoryRedeemDetail" class="modal">
    <div class="modal-content">
        <h2>Log History Redeem Detail</h2>
        <div style="overflow-x: auto;">
            <table class="log-table">
                <thead>
                    <tr>
                        <th>Tanggal Pembelian</th>
                        <th>Nama Paket</th>
                        <th>Tanggal Cetak</th>
                    </tr>
                </thead>
                <tbody id="logHistoryDetailBody">
                    <tr><td colspan="3" class="text-center text-muted">Belum ada data</td></tr>
                </tbody>
            </table>
        </div>
        <button id="closeModalDetail" class="btn-secondary mt-4">Tutup</button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // =================== HISTORY REDEEM (by phone) ===================
    const btnHistory = document.getElementById('btnHistoryRedeem');
    const modalHistory = document.getElementById('logHistoryRedeemModal');
    const closeHistory = document.getElementById('closeModalRedeem');
    const tbodyHistory = document.getElementById('logHistoryBody');

    if (btnHistory) {
        btnHistory.addEventListener('click', async () => {
            const phone = "{{ $customerPhone }}";
            if (!phone) return alert("Nomor telepon belum diisi.");

            const res = await fetch(`{{ route('admin.logHistoryRedeemCustomerPackage') }}?phone=${phone}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await res.json();
            tbodyHistory.innerHTML = '';

            if (data.data?.length > 0) {
                data.data.forEach(item => {
                    tbodyHistory.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${item.purchase_date}</td>
                            <td>${item.package_name}</td>
                            <td>${item.print_date}</td>
                        </tr>
                    `);
                });
            } else {
                tbodyHistory.innerHTML = `<tr><td colspan="3" class="text-center text-muted">Tidak ada log redeem.</td></tr>`;
            }

            modalHistory.style.display = "flex";
        });
    }

    closeHistory.addEventListener('click', () => modalHistory.style.display = "none");


    // =================== HISTORY DETAIL PER PACKAGE ===================
    const detailButtons = document.querySelectorAll('.btnDetailHistory');
    const modalDetail = document.getElementById('logHistoryRedeemDetail');
    const closeDetail = document.getElementById('closeModalDetail');
    const tbodyDetail = document.getElementById('logHistoryDetailBody');

    detailButtons.forEach(btn => {
        btn.addEventListener('click', async function () {
            const id = this.dataset.id;

            const res = await fetch(`{{ route('admin.logHistoryRedeemCustomerPackageDetail') }}?id=${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await res.json();
            tbodyDetail.innerHTML = '';

            if (data.data?.length > 0) {
                data.data.forEach(item => {
                    tbodyDetail.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${item.purchase_date}</td>
                            <td>${item.package_name}</td>
                            <td>${item.print_date}</td>
                        </tr>
                    `);
                });
            } else {
                tbodyDetail.innerHTML = `<tr><td colspan="3" class="text-center text-muted">Tidak ada log redeem.</td></tr>`;
            }

            modalDetail.style.display = "flex";
        });
    });

    closeDetail.addEventListener('click', () => modalDetail.style.display = "none");

});
</script>
@endpush
