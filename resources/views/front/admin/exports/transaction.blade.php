@php
    use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Calibri', sans-serif;
            font-size: 11pt;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
            color: #1a1a1a;
        }

        p.info {
            text-align: center;
            font-size: 10pt;
            color: #555;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #f2f2f2;
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }

        td {
            border: 1px solid #999;
            padding: 6px 8px;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .total {
            text-align: right;
            font-weight: bold;
        }

        .status {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Report - {{ Carbon::today()->format('d F Y') }} - Transaction</h2>
    <p class="info">
        Oleh Staff: <strong>{{ $staff->name ?? $staff->username ?? '-' }}</strong> |
        Jam Export: {{ Carbon::now()->format('H:i:s') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Tanggal</th>
                <th>Items</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $tx)
                <tr>
                    <td>{{ $tx->id }}</td>
                    <td>{{ $tx->customer?->name ?? '-' }}</td>
                    <td>{{ Carbon::parse($tx->created_at)->format('d M Y H:i') }}</td>
                    <td>
                        @foreach ($tx->purchaseDetails as $detail)
                            {{ $detail->name }} (x{{ $detail->qty }})<br>
                        @endforeach
                    </td>
                    <td class="total">Rp {{ number_format($tx->total, 0, ',', '.') }}</td>
                    <td>{{ $tx->payment_label }}</td>
                    <td class="status">
                        @if ($tx->status == 0)
                            Baru
                        @elseif($tx->status == 1)
                            Pending
                        @elseif($tx->status == 2)
                            Paid
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:#999;">Tidak ada transaksi hari ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
