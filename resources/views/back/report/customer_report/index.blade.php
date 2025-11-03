@extends('main.back_blank')

@section('title', 'Report Monthly Visitor')

@section('content')
    <div class="report-customer-page">
        <h2 class="page-title mb-3">Report Monthly Visitor</h2>

        {{-- Filter --}}
        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('report.customer') }}" class="filter-form flex items-end gap-4 flex-wrap">
                {{-- Filter Status --}}
                <div class="form-group">
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="is_active" id="is_active"
                        class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Semua</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>

                {{-- Filter Rentang Bulan --}}
                <div class="form-group">
                    <label for="start_month" class="block text-sm font-medium text-gray-700">Dari Bulan</label>
                    <input type="month" name="start_month" id="start_month"
                        value="{{ request('start_month') }}"
                        class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="form-group">
                    <label for="end_month" class="block text-sm font-medium text-gray-700">Sampai Bulan</label>
                    <input type="month" name="end_month" id="end_month"
                        value="{{ request('end_month') }}"
                        class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                {{-- Tombol --}}
                <div class="form-group">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 mt-6">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="table-section-report relative">
            <div class="table-scroll-container overflow-x-auto">
                <table class="table w-full border-collapse border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-center px-3 py-2 border">No</th>
                            <th class="text-center px-3 py-2 border">Bulan Pembelian</th>
                            <th class="text-center px-3 py-2 border">Jenis Tiket</th>
                            <th class="text-center px-3 py-2 border">Jumlah Pengunjung Per-Tiket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($summary as $index => $data)
                            <tr>
                                <td class="text-center border px-3 py-2">{{ $index + 1 }}</td>
                                <td class="text-center border px-3 py-2">{{ $data['month'] }}</td>
                                <td class="border px-3 py-2">{{ $data['ticket_name'] }}</td>
                                <td class="text-center border px-3 py-2">{{ $data['visitor_count'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-gray-500 py-3 border">
                                    Tidak ada data tiket
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
