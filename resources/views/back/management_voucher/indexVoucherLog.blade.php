@extends('main.back_blank')
@section('title', 'Data Voucher Log')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="voucher-log-page">
        <h2 class="page-title mb-4">Data Detail Voucher</h2>
        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('voucher.log', $id) }}" class="filter-form flex items-end gap-4 flex-wrap">

                {{-- Filter nama Customer --}}
                <div class="form-group">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Customer</label>
                    <input type="text" name="name" id="name" value="{{ request('name') }}"
                        class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="masukkan nama voucher..">
                </div>

                {{-- Filter Code Voucher --}}
                <div class="form-group">
                    <label for="code" class="block text-sm font-medium text-gray-700">Code Voucher</label>
                    <input type="text" name="code" id="code" value="{{ request('code') }}"
                        class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="masukkan code voucher..">
                </div>

                <div class="form-group">
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="is_active" id="is_active"
                        class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Semua</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="form-group">
                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700">
                            Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Print Controls --}}
        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
            <label for="printerSize" class="form-label fw-semibold mb-0" style="white-space: nowrap;">Ukuran Printer:</label>
            <select id="printerSize" class="form-select form-select-sm" style="width: auto; min-width: 120px;">
                <option value="58">58mm</option>
                <option value="80">80mm</option>
            </select>
            <button type="button" class="btn btn-success btn-sm" onclick="printAllVouchers()" {{ $voucherLog->isEmpty() ? 'disabled' : '' }}>
                <i data-feather="printer" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i>
                Print All Code Voucher
            </button>
        </div>

        <div class="table-section mt-2 relative">
            <div class="table-scroll-container">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="text-center">Voucher ID</th>
                                <th class="text-center">Nama Voucher</th>
                                <th class="text-center">Nama Customer</th>
                                <th class="text-center">Code Voucher</th>
                                <th class="text-center">Start At</th>
                                <th class="text-center">End At</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($voucherLog as $item)
                                <tr>
                                    <td>{{ $item->voucher_id }}</td>
                                    <td>{{ $item->voucher->name }}</td>
                                    <td class="text-center">{{ $item->customer_id ?? '-' }}</td>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->start_at }}</td>
                                    <td>{{ $item->end_at }}</td>
                                    <td class="text-center">{!! $item->getBadgeHtml($item->is_active) !!}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="printSingleVoucher('{{ addslashes($item->voucher->name) }}', '{{ $item->code }}', '{{ $item->start_at }}', '{{ $item->end_at }}')"
                                            title="Print Voucher">
                                            <i data-feather="printer" style="width:14px;height:14px;vertical-align:middle;"></i>
                                            Print
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-gray-500 py-3">
                                        Tidak ada data voucher
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden Print Area --}}
    <div id="printArea"></div>

    {{-- Print Styles --}}
    <style>
        /* ===== Screen: hide print area ===== */
        #printArea {
            display: none;
        }

        /* ===== Print Mode ===== */
        @media print {
            /* Hide everything except printArea */
            body * {
                visibility: hidden !important;
            }
            #printArea,
            #printArea * {
                visibility: visible !important;
            }
            #printArea {
                display: block !important;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                z-index: 99999;
            }

            /* Wrapper & Cut Line */
            .voucher-wrapper {
                page-break-inside: avoid;
            }
            .cut-line {
                border-top: 1.5px dashed #000;
                margin: 8mm 0; /* Space luas antar voucher agar mudah dipotong */
                position: relative;
            }
            .cut-line::after {
                content: '✂';
                position: absolute;
                top: -8px;
                left: 10px;
                background: #fff;
                padding: 0 5px;
                font-size: 14px;
                color: #000;
            }
            /* Hilangkan garis potong di voucher terakhir */
            .voucher-wrapper:last-child .cut-line {
                display: none;
            }

            /* ---- 58mm template ---- */
            .print-58 .voucher-card {
                width: 58mm; /* Diperbesar agar pas di kertas 58mm */
                padding: 0;
                margin: 0 auto;
                /* border: 1px solid #000; */
                border-radius: 4mm;
                font-family: 'Segoe UI', Arial, sans-serif;
                font-size: 12px;
                line-height: 1.4;
                box-sizing: border-box;
                background: #fff;
            }
            .print-58 .voucher-card .voucher-label {
                font-size: 10px;
                color: #333;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 2px;
            }
            .print-58 .voucher-card .voucher-value {
                font-size: 14px;
                font-weight: 600;
                color: #000;
                margin-bottom: 3mm;
                word-break: break-all;
            }
            .print-58 .voucher-card .voucher-code {
                font-size: 16px; /* Kode lebih besar */
                font-weight: 700;
                letter-spacing: 1.5px;
                font-family: 'Courier New', monospace;
                text-align: center;
                background: #f8f9fa;
                padding: 4px;
                border-radius: 4px;
                border: 1px solid #eee;
            }
            .print-58 .voucher-card .voucher-header {
                text-align: center;
                font-size: 16px; /* Header lebih besar */
                font-weight: 800;
                margin-bottom: 3mm;
                padding-bottom: 2mm;
                border-bottom: 2px dashed #000;
                text-transform: uppercase;
            }
            .print-58 .voucher-card .voucher-dates {
                display: flex;
                justify-content: space-between;
                font-size: 10px;
                margin-top: 2mm;
                padding-top: 2mm;
                border-top: 1px dashed #ccc;
            }

            /* ---- 80mm template ---- */
            .print-80 .voucher-card {
                width: 80mm;
                padding: 0;
                margin: 0 auto 4mm auto;
                /* border: 1px solid #000; */
                border-radius: 4mm;
                font-family: 'Segoe UI', Arial, sans-serif;
                font-size: 12px;
                line-height: 1.6;
                page-break-inside: avoid;
                box-sizing: border-box;
                background: #fff;
            }
            .print-80 .voucher-card .voucher-label {
                font-size: 9px;
                color: #666;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 0;
            }
            .print-80 .voucher-card .voucher-value {
                font-size: 12px;
                font-weight: 600;
                color: #000;
                margin-bottom: 2.5mm;
                word-break: break-all;
            }
            .print-80 .voucher-card .voucher-code {
                font-size: 14px;
                font-weight: 700;
                letter-spacing: 1.5px;
                font-family: 'Courier New', monospace;
            }
            .print-80 .voucher-card .voucher-header {
                text-align: center;
                font-size: 13px;
                font-weight: 700;
                margin-bottom: 3mm;
                padding-bottom: 2mm;
                border-bottom: 1px dashed #000;
            }
            .print-80 .voucher-card .voucher-dates {
                display: flex;
                justify-content: space-between;
                font-size: 9px;
                margin-top: 2mm;
                padding-top: 2mm;
                border-top: 1px dashed #ccc;
            }

            /* Page settings */
            @page {
                margin: 2mm;
            }
        }
    </style>

    <script>
        /**
         * Build a single voucher card HTML
         */
        function buildVoucherCardHtml(name, code, startAt, endAt) {
            return `
                <div class="voucher-wrapper">
                    <div class="voucher-card">
                        <div class="voucher-header">VOUCHER</div>
                        <div class="voucher-label">Nama Voucher</div>
                        <div class="voucher-value">${name}</div>
                        <div class="voucher-label">Code Voucher</div>
                        <div class="voucher-value voucher-code">${code}</div>
                        <div class="voucher-dates">
                            <div>
                                <span style="color:#666;">Start:</span><br>
                                ${startAt}
                            </div>
                            <div style="text-align:right;">
                                <span style="color:#666;">End:</span><br>
                                ${endAt}
                            </div>
                        </div>
                    </div>
                    <div class="cut-line"></div>
                </div>
            `;
        }

        /**
         * Get the selected printer size class
         */
        function getPrintSizeClass() {
            const size = document.getElementById('printerSize').value;
            return 'print-' + size;
        }

        /**
         * Print single voucher
         */
        function printSingleVoucher(name, code, startAt, endAt) {
            const printArea = document.getElementById('printArea');
            const sizeClass = getPrintSizeClass();
            printArea.className = sizeClass;
            printArea.innerHTML = buildVoucherCardHtml(name, code, startAt, endAt);
            setTimeout(() => { window.print(); }, 100);
        }

        /**
         * Print all vouchers
         */
        function printAllVouchers() {
            const printArea = document.getElementById('printArea');
            const sizeClass = getPrintSizeClass();
            printArea.className = sizeClass;

            let html = '';
            @foreach ($voucherLog as $item)
                html += buildVoucherCardHtml(
                    {!! json_encode($item->voucher->name) !!},
                    {!! json_encode($item->code) !!},
                    {!! json_encode($item->start_at) !!},
                    {!! json_encode($item->end_at) !!}
                );
            @endforeach

            printArea.innerHTML = html;
            setTimeout(() => { window.print(); }, 100);
        }
    </script>
@endsection
