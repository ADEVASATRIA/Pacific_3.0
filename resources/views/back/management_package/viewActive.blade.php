@extends('main.back_blank')
@section('title', 'Data Active Customer Package')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="promo-page">
        <h2 class="page-title">Data Active Customer Package</h2>

        {{-- Filter --}}
        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('package.active.customer') }}"
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
        <div class="table-section mt-4">
            <table class="table w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th>Nama Package</th>
                        <th>Nama Customer</th>
                        <th>Phone</th>
                        <th>Sisa Qty Redeem</th>
                        <th>Total Redeem dilakukan</th>
                        <th>Tanggal Kedaluwarsa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activePackages as $package)
                            <tr>
                                <td>{{ $package->name }}</td>
                                <td>{{ $package->customer->name }}</td>
                                <td>{{ $package->customer->phone }}</td>
                                <td>{{ $package->details->sum('qty_redeemed') }}</td>
                                <td>{{ $package->details->sum('qty_printed') }}</td>
                                <td>{{ \Carbon\Carbon::parse($package->expired_date)->format('d M Y') }}</td>
                            </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-gray-500 py-3">Tidak ada data tiket</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($activePackages->hasPages())
                <div class="mt-4 flex justify-center">
                    {{ $activePackages->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
