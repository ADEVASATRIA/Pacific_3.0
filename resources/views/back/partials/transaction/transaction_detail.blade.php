<div class="space-y-4">
    <div class="border-b pb-2 mb-3">
        <h5 class="font-semibold text-lg">Detail Transaksi</h5>
        <p class="text-sm text-gray-600">
            <strong>Invoice:</strong> {{ $purchase->invoice_no }}<br>
            <strong>Tanggal:</strong> {{ $purchase->created_at->format('d/m/Y H:i') }}<br>
            <strong>Customer:</strong> {{ $purchase->customer->name ?? '-' }}<br>
            <strong>Total:</strong> Rp {{ number_format($purchase->total, 0, ',', '.') }}
        </p>
    </div>

    <table class="table table-sm w-full border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th>Nama Item</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchase->purchaseDetails as $detail)
                <tr>
                    <td>{{ $detail->name ?? '-' }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($detail->qty * $detail->price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
