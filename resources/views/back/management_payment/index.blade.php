@extends('main.back_blank')
@section('title', 'Data Payment Method')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="cloubhouse-page">
        <h2 class="page-title mb-4">Data Payment Method</h2>
        <div class="filter-section mb-4">
            <div class="filter-form flex items-end gap-4 flex-wrap">
                <div class="form-group">
                    <div class="flex items-center gap-2">
                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700"
                            data-bs-toggle="modal" data-bs-target="#modalTambahPaymentMethod">
                            Tambah Payment Method
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-section mt-2 relative">
            <div class="table-scroll-container">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="text-center">Nama Payment Method</th>
                                <th class="text-center">Img</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Provider</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($paymentMethods as $paymentMethod)
                                <tr>
                                    <td>{{ $paymentMethod->name }}</td>
                                    <td class="text-center">
                                        @if ($paymentMethod->img_thumbnail)
                                            <img src="{{ $paymentMethod->img_thumbnail }}" alt="{{ $paymentMethod->name }}" class="w-12 h-12 rounded-full">
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $paymentMethod->type }}</td>
                                    <td class="text-center">
                                        @if ($paymentMethod->provider)
                                            {{ $paymentMethod->provider }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">{!! $paymentMethod->getBadgeHtml($paymentMethod->is_active) !!}</td>
                                    <td class="text-center">
                                        <button class="btn btn-primary btn-sm"
                                            onclick="openEditModal({{ $paymentMethod->id }})">
                                            Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="openConfirmModal({{ $paymentMethod->id }}, '{{ $paymentMethod->name }}')">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-gray-500 py-3">
                                        Tidak ada data staff
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    {{-- Modal Tambah Payment Method --}}
        <div class="modal fade" id="modalTambahPromo" tabindex="-1" aria-labelledby="modalTambahPromoLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold text-black" id="modalTambahPromoLabel">
                        <i class="fas fa-tags me-2"></i> Tambah Promo Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    <form action="{{ route('add.payment-method') }}" method="POST" id="formTambahPaymentMethod" class="needs-validation"
                        novalidate>
                        @csrf

                        <div class="container-fluid">
                            <div class="row g-4">

                                {{-- Nama --}}
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>

                                {{-- Gambar --}}
                                <div class="col-md-6">
                                    <label for="image" class="form-label">Gambar</label>
                                    <input type="file" class="form-control" id="image" name="image" required>
                                </div>

                                {{-- Type --}}
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="">Pilih Type</option>
                                        <option value="cash">Cash</option>
                                        <option value="qris">Qris</option>
                                        <option value="debit">Debit</option>
                                        <option value="bank_transfer">Transfer Bank</option>
                                        <option value="voucher">Voucher</option>
                                    </select>
                                </div>
                                

                            </div>
                        </div>
                    </form>
                </div>

                {{-- Footer --}}
                <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" form="formTambahPromo" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection
