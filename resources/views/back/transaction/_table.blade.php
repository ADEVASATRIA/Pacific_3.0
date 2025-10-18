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
                <td>{{ $purchase->payment_label }}</td>
                <td>Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                <td>
                    <button type="button" class="rounded-md bg-blue-600 text-white px-3 py-1 text-sm hover:bg-blue-700"
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

@if ($purchases->hasPages())
    <div class="mt-4 flex justify-center">
        {{ $purchases->links('pagination::bootstrap-5') }}
    </div>
@endif

