@extends('main.back_blank')
@section('title', 'Data Detail Package')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="promo-page">
        <h2 class="page-title">Data Detail Package</h2>

        {{-- Filter --}}
        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('package.detail.customer') }}"
                class="filter-form flex items-end gap-4 flex-wrap">
                <div class="form-group">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ request('phone') }}"
                        class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="form-group">
                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700">
                            Filter
                        </button>
                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700"
                            data-bs-toggle="modal" data-bs-target="#modalTambahPackageCombo">
                            Tambah Package
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table Section --}}
        @if ($customer)
            <div class="table-section mt-4">
                <table class="table w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th>Tanggal Pembelian</th>
                            <th>Nama Customer</th>
                            <th>Nomor Telepon</th>
                            <th>Total Jumlah Dibayar</th>
                            <th>Jenis Paket</th>
                            <th>Total Redeem</th>
                            <th>Sisa Qty Redeem</th>
                            <th>Tanggal Kedaluwarsa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchases ?? [] as $purchase)
                        @foreach ($purchase->purchaseDetails as $detail)
                            @php
                                $redeem = $detail->packageComboRedeem;
                            @endphp
                            <tr>
                                <td>{{ $purchase->created_at?->locale('id')->translatedFormat('d F Y') }}</td>
                                <td>{{ $purchase->customer->name }}</td>
                                <td>{{ $purchase->customer->phone }}</td>
                                <td class="text-center">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                                <td>{{ $redeem->name }}</td>
                                <td class="text-center">{{ $redeem->details->sum('qty_printed') }}</td>
                                <td class="text-center">{{ $redeem->details->sum('qty_redeemed') }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($redeem->expired_date)->locale('id')->translatedFormat('d F Y') }}</td>
                                {{-- <td>
                                    <button class="btn-history btnDetailHistory" data-id="{{ $redeem->id }}">
                                        <i data-lucide="book"></i>
                                        History Redeem
                                    </button>
                                </td> --}}
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">Tidak ada data pembelian paket ditemukan.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-data">
                <p>Masukkan nomor telepon untuk melihat data paket pelanggan.</p>
            </div>
        @endif
    </div>
@endsection
