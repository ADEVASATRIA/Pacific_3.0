@extends('main.back_blank')
@section('title', 'Detail Shift Report')
@vite('resources/css/back/report/detail_shift_session.css')
@section('content')
<div class="srd-wrapper">

    {{-- ═══ HEADER ══════════════════════════════════════════ --}}
    <div class="srd-header">
        <div class="srd-title-group">
            <a href="{{ route('shift-report') }}" class="srd-back-btn">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
            </a>
            <div>
                <h1 class="srd-title">Detail Shift</h1>
                <div class="srd-badge-shift" style="margin-top:5px;">
                    <svg width="10" height="10" fill="currentColor" viewBox="0 0 20 20">
                        <circle cx="10" cy="10" r="10"/>
                    </svg>
                    {{ $session->staff->username }}
                </div>
            </div>
        </div>
        <div class="srd-meta">
            <span class="srd-meta-label">Waktu Buka</span>
            <span class="srd-meta-value">📅 {{ \Carbon\Carbon::parse($session->waktu_buka)->format('d M Y, H:i') }}</span>
        </div>
    </div>

    {{-- ═══ SUMMARY CARDS ═════════════════════════════════════ --}}
    <div class="srd-summary-grid">

        {{-- Saldo Awal --}}
        <div class="srd-stat-card c-slate">
            <div class="srd-stat-icon">💰</div>
            <span class="srd-stat-label">Saldo Awal</span>
            <span class="srd-stat-value">Rp {{ number_format($session->saldo_awal, 0, ',', '.') }}</span>
        </div>

        {{-- Saldo Akhir --}}
        <div class="srd-stat-card c-blue">
            <div class="srd-stat-icon">🏦</div>
            <span class="srd-stat-label">Saldo Akhir</span>
            <span class="srd-stat-value">Rp {{ number_format($session->saldo_akhir, 0, ',', '.') }}</span>
        </div>

        {{-- Total Kolam --}}
        <div class="srd-stat-card c-teal">
            <div class="srd-stat-icon">🏊</div>
            <span class="srd-stat-label">Total Kolam</span>
            <span class="srd-stat-value">Rp {{ number_format($session->penjualan_fnb_kolam, 0, ',', '.') }}</span>
        </div>

        {{-- Total Cafe --}}
        <div class="srd-stat-card c-amber">
            <div class="srd-stat-icon">☕</div>
            <span class="srd-stat-label">Total Cafe</span>
            <span class="srd-stat-value">Rp {{ number_format($session->penjualan_fnb_cafe, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- ═══ TWO-COLUMN PANELS ══════════════════════════════════ --}}
    <div class="srd-panels">

        {{-- LEFT: Cash In / Out --}}
        <div class="srd-panel">
            <div class="srd-panel-header">
                <div class="srd-panel-icon" style="background:#f0fdf4;">💸</div>
                <h6 class="srd-panel-title">Cash In / Out</h6>
                <span class="srd-panel-count">{{ $session->cashInOut->count() }} item</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="srd-table">
                    <thead>
                        <tr>
                            <th>Tipe & Keterangan</th>
                            <th class="text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($session->cashInOut as $cio)
                            <tr>
                                <td>
                                    @if($cio->type == 1)
                                        <span class="srd-pill p-in">
                                            <span class="srd-dot d-in"></span> Cash In
                                        </span>
                                    @else
                                        <span class="srd-pill p-out">
                                            <span class="srd-dot d-out"></span> Cash Out
                                        </span>
                                    @endif
                                    <span class="srd-keterangan">{{ $cio->keterangan ?? '—' }}</span>
                                </td>
                                <td class="text-right">
                                    @if($cio->type == 1)
                                        <span class="srd-amount-in">+Rp {{ number_format($cio->nominal_uang, 0, ',', '.') }}</span>
                                    @else
                                        <span class="srd-amount-out">-Rp {{ number_format($cio->nominal_uang, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">
                                    <div class="srd-empty">
                                        <div class="srd-empty-icon">📭</div>
                                        <div class="srd-empty-text">Tidak ada data cash in/out</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- RIGHT: Transaksi Terbayar --}}
        <div class="srd-panel">
            <div class="srd-panel-header">
                <div class="srd-panel-icon" style="background:#eff6ff;">🧾</div>
                <h6 class="srd-panel-title">Transaksi Terbayar</h6>
                <span class="srd-panel-count">{{ $purchases->count() }} transaksi</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="srd-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Metode</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td>
                                    <span style="font-size:12px;font-weight:700;color:#2563eb;display:block;">
                                        {{ $purchase->invoice_no }}
                                    </span>
                                    <span style="font-size:10.5px;color:#94a3b8;">
                                        {{ $purchase->created_at->format('H:i:s') }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size:12px;font-weight:600;color:#334155;">
                                        {{ $purchase->customer->name ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="srd-pill p-pay">
                                        {{ $purchase->paymentMethod->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <span class="srd-amount">
                                        Rp {{ number_format($purchase->total, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="srd-empty">
                                        <div class="srd-empty-icon">📋</div>
                                        <div class="srd-empty-text">Tidak ada transaksi dalam periode shift ini</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- end panels --}}

</div>{{-- end wrapper --}}

@endsection
