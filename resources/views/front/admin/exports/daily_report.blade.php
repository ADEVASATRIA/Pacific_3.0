@php
    use Carbon\Carbon;
@endphp

{{-- Add style for page break control --}}
<style>
    table { page-break-inside: auto; }
    tr { page-break-inside: avoid; page-break-after: auto; }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
</style>

<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 11pt; margin: 0; padding: 0;">
    {{-- ===== HEADER TITLE ===== --}}
    <tr>
        <td colspan="7" style="border: 2px solid #000; padding: 15px; text-align: center; font-size: 16pt; font-weight: bold; background-color: #f5f5f5;">
            LAPORAN HARIAN KASIR
        </td>
    </tr>
    
    {{-- ===== SPACER ===== --}}
    <tr><td colspan="7" style="height: 15px; border: none;"></td></tr>
    
    {{-- ===== INFO BOX ===== --}}
    <tr>
        <td colspan="2" style="border: 1px solid #000; padding: 10px 15px; font-weight: bold; background-color: #f9f9f9; width: 25%;">Tanggal Laporan</td>
        <td colspan="5" style="border: 1px solid #000; padding: 10px 15px; width: 75%;">{{ Carbon::today()->format('d F Y') }}</td>
    </tr>
    <tr>
        <td colspan="2" style="border: 1px solid #000; padding: 10px 15px; font-weight: bold; background-color: #f9f9f9;">Nama Staff</td>
        <td colspan="5" style="border: 1px solid #000; padding: 10px 15px;">{{ $staff->name ?? $staff->username ?? '-' }}</td>
    </tr>
    <tr>
        <td colspan="2" style="border: 1px solid #000; padding: 10px 15px; font-weight: bold; background-color: #f9f9f9;">Waktu Buka</td>
        <td colspan="5" style="border: 1px solid #000; padding: 10px 15px;">{{ isset($cashSession->waktu_buka) ? Carbon::parse($cashSession->waktu_buka)->format('H:i') : '-' }}</td>
    </tr>
    <tr>
        <td colspan="2" style="border: 1px solid #000; padding: 10px 15px; font-weight: bold; background-color: #f9f9f9;">Waktu Tutup</td>
        <td colspan="5" style="border: 1px solid #000; padding: 10px 15px;">{{ Carbon::now()->format('H:i') }}</td>
    </tr>
    <tr>
        <td colspan="2" style="border: 1px solid #000; padding: 10px 15px; font-weight: bold; background-color: #f9f9f9;">Saldo Awal</td>
        <td colspan="5" style="border: 1px solid #000; padding: 10px 15px;">Rp {{ number_format($cashSession->saldo_awal ?? 0, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td colspan="2" style="border: 1px solid #000; padding: 10px 15px; font-weight: bold; background-color: #f9f9f9;">Saldo Akhir</td>
        <td colspan="5" style="border: 1px solid #000; padding: 10px 15px;">Rp {{ number_format($cashSession->saldo_akhir ?? 0, 0, ',', '.') }}</td>
    </tr>
    
    {{-- ===== SPACER ===== --}}
    <tr><td colspan="7" style="height: 20px; border: none;"></td></tr>
    
    {{-- ===== TABLE HEADER ===== --}}
    <tr style="background-color: #e0e0e0;">
        <th style="border: 1px solid #000; padding: 12px 8px; text-align: center; font-weight: bold; width: 12%;">Metode<br>Pembayaran</th>
        <th style="border: 1px solid #000; padding: 12px 8px; text-align: center; font-weight: bold; width: 14%;">List Customer</th>
        <th style="border: 1px solid #000; padding: 12px 8px; text-align: center; font-weight: bold; width: 14%;">Phone Customer</th>
        <th style="border: 1px solid #000; padding: 12px 8px; text-align: center; font-weight: bold; width: 28%;">Items Detail</th>
        <th style="border: 1px solid #000; padding: 12px 8px; text-align: center; font-weight: bold; width: 8%;">Status</th>
        <th style="border: 1px solid #000; padding: 12px 8px; text-align: center; font-weight: bold; width: 8%;">Waktu</th>
        <th style="border: 1px solid #000; padding: 12px 8px; text-align: center; font-weight: bold; width: 16%;">Total</th>
    </tr>
    
    {{-- ===== TRANSACTION DATA ===== --}}
    @if(isset($groupedTransactions) && count($groupedTransactions) > 0)
        @foreach($groupedTransactions as $paymentMethod => $transactions)
            @php 
                $subtotal = 0;
            @endphp

            @foreach($transactions as $index => $tx)
                @php $subtotal += $tx->total; @endphp
                <tr style="page-break-inside: avoid;">
                    {{-- Display payment method on every row instead of using rowspan to prevent PDF page break issues --}}
                    <td style="border: 1px solid #000; padding: 10px 8px; vertical-align: top; font-weight: bold;">
                        @if($index === 0)
                            {{ $paymentMethod }}
                        @else
                            {{-- Empty cell but keep border for visual consistency --}}
                            &nbsp;
                        @endif
                    </td>
                    
                    <td style="border: 1px solid #000; padding: 10px 8px; vertical-align: top;">{{ $tx->customer?->name ?? 'Guest' }}</td>
                    <td style="border: 1px solid #000; padding: 10px 8px; vertical-align: top;">{{ $tx->customer?->phone ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 10px 8px; vertical-align: top; line-height: 1.6;">
                        @foreach ($tx->purchaseDetails as $detail)
                            {{ $detail->name }} (x{{ $detail->qty }})<br>
                        @endforeach
                    </td>
                    <td style="border: 1px solid #000; padding: 10px 8px; text-align: center; vertical-align: top;">
                        @if ($tx->status == 0) Baru
                        @elseif($tx->status == 1) Pending
                        @elseif($tx->status == 2) Paid
                        @endif
                    </td>
                    <td style="border: 1px solid #000; padding: 10px 8px; text-align: center; vertical-align: top;">{{ Carbon::parse($tx->created_at)->format('H:i') }}</td>
                    <td style="border: 1px solid #000; padding: 10px 8px; text-align: right; vertical-align: top;">Rp {{ number_format($tx->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            {{-- SUBTOTAL ROW --}}
            <tr style="background-color: #f5f5f5; page-break-inside: avoid;">
                <td colspan="6" style="border: 1px solid #000; padding: 12px 10px; font-weight: bold;">SUB TOTAL {{ strtoupper($paymentMethod) }}</td>
                <td style="border: 1px solid #000; padding: 12px 10px; text-align: right; font-weight: bold;">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach

        {{-- GRAND TOTAL ROW --}}
        <tr style="background-color: #2c3e50; color: #ffffff; page-break-inside: avoid;">
            <td colspan="6" style="border: 1px solid #000; padding: 14px 10px; font-weight: bold; font-size: 12pt;">GRAND TOTAL</td>
            <td style="border: 1px solid #000; padding: 14px 10px; text-align: right; font-weight: bold; font-size: 12pt;">Rp {{ number_format($totalAll, 0, ',', '.') }}</td>
        </tr>
    @else
        <tr>
            <td colspan="7" style="border: 1px solid #000; padding: 25px; text-align: center;">Tidak ada transaksi hari ini.</td>
        </tr>
    @endif
</table>
