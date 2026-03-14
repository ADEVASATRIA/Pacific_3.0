@extends('main.back_blank')
@section('title', 'Data Shift Report')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="shift-report-page">
        <h2 class="page-title mb-4">Data Shift Report</h2>
        {{-- Filter --}}
        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('shift-report') }}" class="filter-form flex items-end gap-4 flex-wrap">

                {{-- Filter nama --}}
                <div class="form-group">
                    <label for="staff_name" class="block text-sm font-medium text-gray-700">Nama Staff</label>
                    <input type="text" name="staff_name" id="staff_name" value="{{ request('staff_name') }}"
                        class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        placeholder="masukkan nama staff..">
                </div>

                {{-- Filter waktu buka --}}
                <div class="form-group">
                    <label for="waktu_buka" class="block text-sm font-medium text-gray-700">Waktu Buka</label>
                    <input type="date" name="waktu_buka" id="waktu_buka" value="{{ request('waktu_buka') }}"
                        class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                {{-- Filter waktu tutup --}}
                <div class="form-group">
                    <label for="waktu_tutup" class="block text-sm font-medium text-gray-700">Waktu Tutup</label>
                    <input type="date" name="waktu_tutup" id="waktu_tutup" value="{{ request('waktu_tutup') }}"
                        class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="form-group">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status"
                        class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Semua</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Buka</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tutup</option>
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

        <div class="table-section mt-2 relative">
            <div class="table-scroll-container">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="text-center">Staff ID</th>
                                <th class="text-center">Nama Staff</th>
                                <th class="text-center">Saldo Awal</th>
                                <th class="text-center">Saldo Akhir</th>
                                <th class="text-center">Penjualan FnB Kolam</th>
                                <th class="text-center">Penjualan Fnb Cafe</th>
                                <th class="text-center">Waktu Buka</th>
                                <th class="text-center">Waktu Tutup</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($CashSession as $cashSession)
                                <tr>
                                    <td class="text-center">{{ $cashSession->staff_id }}</td>
                                    <td class="text-center">{{ $cashSession->staff->username }}</td>
                                    <td class="text-center">
                                        {{ $cashSession->saldo_awal ? 'Rp. ' . number_format($cashSession->saldo_awal, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $cashSession->saldo_akhir ? 'Rp. ' . number_format($cashSession->saldo_akhir, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $cashSession->penjualan_fnb_kolam ? 'Rp. ' . number_format($cashSession->penjualan_fnb_kolam, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $cashSession->penjualan_fnb_cafe ? 'Rp. ' . number_format($cashSession->penjualan_fnb_cafe, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-center">{{ $cashSession->waktu_buka ?? '-' }}</td>
                                    <td class="text-center">{{ $cashSession->waktu_tutup ?? '-' }}</td>
                                    <td class="text-center">{!! $cashSession->getBadgeHtml($cashSession->status) !!}</td>
                                    <td class="text-center">
                                        <a href="{{ route('shift-report.detail', $cashSession->id) }}"
                                            class="btn btn-primary btn-sm text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition-all text-sm font-bold">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-gray-500 py-3">
                                        Tidak ada data shift report
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if ($CashSession->hasPages())
                {{ $CashSession->appends(request()->query())->links('pagination::bootstrap-5') }}
            @endif
        </div>
    </div>
@endsection
