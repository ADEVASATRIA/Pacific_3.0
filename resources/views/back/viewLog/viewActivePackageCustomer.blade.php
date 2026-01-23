@extends('main.back_blank')
@section('title', 'View Active Package Customer')

@section('content')
    <div class="ticket-types-page">
        <h2 class="page-title">View Active Package Customer</h2>

        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('view-active-package-customer') }}"
                class="filter-form flex items-end gap-4 flex-wrap">
                <div class="form-group">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                    <input type="text" name="phone" id="phone" placeholder="Cari nomor telepon..."
                        value="{{ $phone ?? '' }}"
                        class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm">
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
            <div class="table-wrapper">
                <div class="table-scroll-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Package</th>
                                <th>Nama Customer</th>
                                <th>Phone</th>
                                <th>Sisa Qty Redeem</th>
                                <th>Total Redeem dilakukan</th>
                                <th>Tanggal Kadaluwarsa</th>
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
                                    <td colspan="13" class="text-center text-gray-500 py-3">
                                        Tidak ada data tiket
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script>

    </script>
@endsection