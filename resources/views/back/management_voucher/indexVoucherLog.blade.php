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
                                <!-- <th class="text-center">Aksi</th> -->
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
                                    <!-- <td class="text-center">
                                        <button class="btn btn-primary btn-sm"
                                            onclick="openEditModal({{ $item->id }})">
                                            Edit
                                        </button>
                                    </td> -->
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-gray-500 py-3">
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
@endsection
