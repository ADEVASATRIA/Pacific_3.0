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
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases ?? [] as $purchase)
                        @foreach ($purchase->purchaseDetails as $detail)
                            @php
                                $redeem = $detail->packageComboRedeem ?? null;
                                $expiredDate = $redeem ? $redeem->expired_date : null;
                            @endphp
                            <tr>
                                <td>{{ $purchase->created_at?->locale('id')->translatedFormat('d F Y') ?? '-' }}</td>
                                <td>{{ $purchase->invoice_no ?? '-' }}</td>
                                <td>{{ $purchase->customer->name ?? '-' }}</td>
                                <td>{{ $purchase->customer->phone ?? '-' }}</td>
                                <td class="text-end">Rp {{ number_format($purchase->total ?? 0, 0, ',', '.') }}</td>
                                <td>{{ $redeem->name ?? '-' }}</td>
                                <td>{{ $redeem?->details->sum('qty_printed') ?? 0 }}</td>
                                <td>{{ $redeem?->details->sum('qty_redeemed') ?? 0 }}</td>
                                <td>{{ $expiredDate ? \Carbon\Carbon::parse($expiredDate)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="9" class="text-center text-muted">Tidak ada data pembelian paket ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @else
            <div class="no-data"><p>Masukkan nomor telepon untuk melihat data paket pelanggan.</p></div>
        @endif
    </div>
@endsection


{{-- MODAL --}}
<div id="logHistoryRedeemModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle">Log History Redeem</h2>
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
        <button id="closeModal" class="btn-secondary mt-4">Tutup</button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnHistory = document.getElementById('btnHistoryRedeem');
    const modal = document.getElementById('logHistoryRedeemModal');
    const tbody = document.getElementById('logHistoryBody');
    const closeModal = document.getElementById('closeModal');

    if (btnHistory) {
        btnHistory.addEventListener('click', async function() {
            const phone = "{{ $customerPhone ?? '' }}";
            if (!phone) return alert('Nomor telepon belum diisi.');

            try {
                const res = await fetch(`{{ route('admin.logHistoryRedeemCustomerPackage') }}?phone=${phone}`, {
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (!res.ok) throw new Error('Network response was not ok');
                
                const data = await res.json();

                tbody.innerHTML = '';
                if (data.data && data.data.length > 0) {
                    data.data.forEach(item => {
                        const row = `<tr>
                            <td>${item.purchase_date}</td>
                            <td>${item.package_name}</td>
                            <td>${item.print_date}</td>
                        </tr>`;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted">Tidak ada log redeem.</td></tr>`;
                }

                modal.style.display = 'flex';
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil data.');
            }
        });
    }

    if (closeModal) {
        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});
</script>
@endpush
