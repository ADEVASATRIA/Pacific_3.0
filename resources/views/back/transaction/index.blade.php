@extends('main.back-office')
@section('title', 'Data Transaksi')

@section('content')
    <div class="transaction-page">
        <h2 class="page-title">Data Transaksi</h2>

        <div class="content-area" id="contentArea">
            {{-- Filter Section --}}
            <div class="filter-section mb-4">
                <form method="GET" action="{{ route('transaction') }}" id="filterForm"
                    class="filter-form flex items-end gap-4 flex-wrap">

                    {{-- Filter nama customer --}}
                    <div class="form-group">
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Customer</label>
                        <input type="text" name="nama" id="nama" value="{{ $nama ?? '' }}"
                            class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Cari nama customer...">
                    </div>

                    {{-- Tanggal Mulai --}}
                    <div class="form-group">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate ?? '' }}"
                            class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    {{-- Tanggal Akhir --}}
                    <div class="form-group">
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate ?? '' }}"
                            class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    {{-- Jenis Pembayaran --}}
                    <div class="form-group">
                        <label for="payment_type" class="block text-sm font-medium text-gray-700">Jenis Pembayaran</label>
                        <select name="payment_type" id="payment_type"
                            class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Semua</option>
                            <option value="1" {{ $paymentType == 1 ? 'selected' : '' }}>Cash</option>
                            <option value="2" {{ $paymentType == 2 ? 'selected' : '' }}>QRIS BCA</option>
                            <option value="3" {{ $paymentType == 3 ? 'selected' : '' }}>QRIS Mandiri</option>
                            <option value="4" {{ $paymentType == 4 ? 'selected' : '' }}>Debit BCA</option>
                            <option value="5" {{ $paymentType == 5 ? 'selected' : '' }}>Debit Mandiri</option>
                            <option value="6" {{ $paymentType == 6 ? 'selected' : '' }}>Transfer</option>
                            <option value="7" {{ $paymentType == 7 ? 'selected' : '' }}>QRIS BRI</option>
                            <option value="8" {{ $paymentType == 8 ? 'selected' : '' }}>Debit BRI</option>
                        </select>
                    </div>

                    {{-- Tombol Filter --}}
                    <div class="form-group">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            {{-- Table Section --}}
            <div class="table-section">
                <table class="table w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th>No</th>
                            <th>No Invoice</th>
                            <th>Tanggal</th>
                            <th>Nama Customer</th>
                            <th>Metode Pembayaran</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchases as $index => $purchase)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $purchase->invoice_no ?? '-' }}</td>
                                <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $purchase->customer->name ?? '-' }}</td>
                                <td>
                                    @switch($purchase->payment)
                                        @case(1)
                                            Cash
                                        @break

                                        @case(2)
                                            QRIS BCA
                                        @break

                                        @case(3)
                                            QRIS Mandiri
                                        @break

                                        @case(4)
                                            Debit BCA
                                        @break

                                        @case(5)
                                            Debit Mandiri
                                        @break

                                        @case(6)
                                            Transfer
                                        @break

                                        @case(7)
                                            QRIS BRI
                                        @break

                                        @case(8)
                                            Debit BRI
                                        @break

                                        @default
                                            -
                                    @endswitch
                                </td>
                                <td>Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                                <td>
                                    <button type="button"
                                        class="rounded-md bg-blue-600 text-white px-3 py-1 text-sm hover:bg-blue-700"
                                        onclick="showTransactionDetail({{ $purchase->id }})">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-gray-500">
                                        Tidak ada transaksi ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    @if ($purchases->hasPages())
                        <div class="mt-4 flex justify-center">
                            {{ $purchases->links('vendor.pagination.custom-tailwind') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endsection

    {{-- Pindahkan offcanvas ke luar section content --}}
    @push('after-content')
        <div class="offcanvas offcanvas-end" tabindex="-1" id="transactionDetailCanvas"
            aria-labelledby="transactionDetailLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="transactionDetailLabel">Detail Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" id="transactionDetailBody">
                {{-- kosong dulu, tidak ada teks default --}}
            </div>
        </div>
    @endpush

    @push('scripts')
        <script>
            function showTransactionDetail(purchaseId) {
                const offcanvasElement = document.getElementById('transactionDetailCanvas');
                const offcanvasBody = document.getElementById('transactionDetailBody');
                offcanvasBody.innerHTML = '<p class="text-muted">Memuat data...</p>';

                fetch(`/transaction/detail/${purchaseId}`)
                    .then(res => res.text())
                    .then(html => offcanvasBody.innerHTML = html)
                    .catch(() => offcanvasBody.innerHTML = `<p class="text-danger">Gagal memuat data.</p>`);

                new bootstrap.Offcanvas(offcanvasElement).show();
            }
        </script>
    @endpush
