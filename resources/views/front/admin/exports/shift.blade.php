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
    <h2>Laporan Shift Hari Ini - {{ Carbon::today()->format('d F Y') }}</h2>
    <p class="info">
        Oleh Staff: <strong>{{ $staff->name ?? $staff->username ?? '-' }}</strong> |
        Jam Export: {{ Carbon::now()->format('H:i:s') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Nama Staff</th>
                <th>Saldo Awal</th>
                <th>Saldo Akhir</th>
                <th>Penjualan FnB</th>
                <th>Minus Balance</th>
                <th>Waktu Buka</th>
                <th>Waktu Tutup</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($shift as $sh)
                <tr>
                    <td>{{ $sh->staff?->name ?? '-' }}</td>
                    <td>Rp {{ number_format($sh->saldo_awal, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($sh->saldo_akhir, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($sh->pendapatan_fnb, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($sh->minus_balance, 0, ',', '.') }}</td>
                    <td>{{ $sh->waktu_buka }}</td>
                    <td>{{ $sh->waktu_tutup }}</td>
                    <td class="status">
                        @if ($sh->status == 0)
                            Di Tutup
                        @elseif($sh->status == 1)
                            Di Buka
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; color:#999;">Tidak ada shift hari ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
