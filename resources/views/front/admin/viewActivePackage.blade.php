@extends('front.admin.index')

@section('title', 'Data Package Active')
@section('page-title', 'Daftar Package Active Pacific')
@vite('resources/css/admin/package.css')
@stack('styles')

@section('top-controls')
    <form method="GET" class="filter-bar">
        <div class="filter-group">
            <div class="form-field">
                <label>No. Telepon</label>
                <input type="text" name="phone" value="{{ request('phone') }}" placeholder="08xxxx">
            </div>
        </div>
        <button type="submit" class="btn primary">Terapkan Filter</button>
        <a href="{{ route('admin.viewActivePackageCustomer') }}" class="btn reset">Reset</a>
    </form>
@endsection

@section('content')
    <section class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Package</th>
                    <th>Nama Customer</th>
                    <th>Phone</th>
                    <th>Sisa Qty Redeem</th>
                    <th>Total Redeem dilakukan</th>
                    <th>Tanggal kadaluwarsa</th>
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
    </section>
@endsection
