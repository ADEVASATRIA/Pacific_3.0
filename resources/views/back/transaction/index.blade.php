@extends('main.back_blank')
@section('title', 'Data Transaksi')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="transaction-page">
        <h2 class="page-title">Data Transaksi</h2>

        {{-- Filter Section --}}
        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('transaction') }}" class="filter-form flex items-end gap-4 flex-wrap">
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
            <div class="table-wrapper">
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
                                        class="btn btn-secondary btn-sm"
                                        onclick="showTransactionDetail({{ $purchase->id }})">
                                        Detail
                                    </button>
                                    <button type="button"
                                        class="btn btn-danger btn-sm"
                                        onclick="openConfirmModal({{ $purchase->id }}, '{{ $purchase->invoice_no }}')">
                                        Hapus
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
            </div>
            {{-- Pagination --}}
            @if ($purchases->hasPages())
                <div class="mt-4 flex justify-center">
                    {{ $purchases->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

            {{-- Offcanvas --}}
            <div class="offcanvas offcanvas-end" tabindex="-1" id="transactionDetailCanvas"
                aria-labelledby="transactionDetailLabel" style="--bs-offcanvas-width: 600px;">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="transactionDetailLabel">Detail Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" id="transactionDetailBody">
                    <p class="text-muted">Memuat data...</p>
                </div>
            </div>
        </div>

        {{-- Modal Delete --}}
        <div id="confirmDeleteModal" class="closecashier-modal">
            <div class="closecashier-modal-content">
                <h2>Hapus Data Transaksi</h2>
                <div class="closecashier-body">
                    <p id="deleteTransactionInfo">Apakah Anda yakin ingin menghapus Data ini?</p>
                </div>
                <div class="closecashier-footer">
                    <button id="btnCancelDelete" class="btn-danger">Batal</button>
                    <form id="deleteTransactionForm" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-success">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Success --}}
        <div id="successModal" class="success-modal">
            <div class="success-modal-content">
                <div class="success-icon">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none" />
                        <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                    </svg>
                </div>
                <h3 class="success-title">Data Transaksi Berhasil Dihapus!</h3>
                <p class="success-message">Transaksi telah dihapus dari sistem.</p>
            </div>
        </div>

        <script>
            // Logic untuk modal delete
            const confirmModal = document.getElementById('confirmDeleteModal');
            const deleteTransactionForm = document.getElementById('deleteTransactionForm');
            const deleteTransactionInfo = document.getElementById('deleteTransactionInfo');
            const cancelBtn = document.getElementById('btnCancelDelete');

            function openConfirmModal(id, invoiceNo) {
                confirmModal.style.display = 'flex';
                deleteTransactionInfo.innerHTML =
                    `<p>Apakah Anda yakin ingin menghapus transaksi dengan invoice <strong>${invoiceNo}</strong>?</p>`;
                deleteTransactionForm.action = `/delete-transaction/${id}`;
            }

            cancelBtn.addEventListener('click', () => {
                confirmModal.style.display = 'none';
            });

            // Modal success feedback
            @if (session('success'))
                window.addEventListener('load', () => {
                    const successModal = document.getElementById('successModal');
                    successModal.style.display = 'flex';
                    setTimeout(() => {
                        successModal.style.display = 'none';
                    }, 2500);
                });
            @endif

            @if (session('error'))
                window.addEventListener('load', () => {
                    alert(@json(session('error')));
                });
            @endif
        </script>

        <script>
            function showTransactionDetail(purchaseId) {
                const offcanvasElement = document.getElementById('transactionDetailCanvas');
                const offcanvasBody = document.getElementById('transactionDetailBody');

                offcanvasBody.innerHTML = '<p class="text-muted">Memuat data...</p>';

                fetch(`/transaction/detail/${purchaseId}`)
                    .then(res => {
                        if (!res.ok) throw new Error('Gagal memuat data');
                        return res.text();
                    })
                    .then(html => {
                        offcanvasBody.innerHTML = html;
                    })
                    .catch(err => {
                        offcanvasBody.innerHTML = `<p class="text-danger">${err.message}</p>`;
                    });

                const bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
                bsOffcanvas.show();
            }
        </script>
    @endsection
