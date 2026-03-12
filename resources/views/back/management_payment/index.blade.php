@extends('main.back_blank')
@section('title', 'Data Payment Method')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="payment-method-page">
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
                                <th class="text-center">Req Approval Code</th>
                                <th class="text-center">Req Number Card</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($paymentMethods as $paymentMethod)
                                <tr>
                                    <td>{{ $paymentMethod->name }}</td>
                                    <td class="text-center">
                                        @if ($paymentMethod->img_thumbnail)
                                            <img src="{{ asset('storage/' . $paymentMethod->img_thumbnail) }}" alt="{{ $paymentMethod->name }}" class="w-12 h-12 rounded-full">
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $paymentMethod->type->name }}</td>
                                    <td class="text-center">
                                        @if ($paymentMethod->provider)
                                            {{ $paymentMethod->provider }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">{!! $paymentMethod->getBadgeHtml($paymentMethod->is_active) !!}</td>
                                    <td class="text-center">{!! $paymentMethod->getBadgeHtml($paymentMethod->is_approval_code_required) !!}</td>
                                    <td class="text-center">{!! $paymentMethod->getBadgeHtml($paymentMethod->is_number_card_required) !!}</td>
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

    {{-- Modal Add Payment Method --}}
    <div class="modal fade" id="modalTambahPaymentMethod" tabindex="-1" aria-labelledby="modalTambahPaymentMethodLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold text-black" id="modalTambahPaymentTypesLabel">
                        <i class="fas fa-tags me-2"></i> Tambah Payment Method Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    {{-- Ganti formTambahPromo ke formTambahPaymentMethod --}}
                    <form action="{{ route('add.payment-method') }}" method="POST" id="formTambahPaymentMethod" class="needs-validation"
                        novalidate enctype="multipart/form-data">
                        @csrf

                        <div class="container-fluid">
                            <div class="row g-4">

                                {{-- Nama Payment Method --}}
                                <div class="col-md-6">
                                    <label for="add_name" class="form-label fw-semibold">Nama Payment Method</label>
                                    <input type="text" name="name" id="add_name" class="form-control"
                                        placeholder="Masukkan nama Payment Method" required value="{{ old('name') }}">
                                    <div class="invalid-feedback">Nama Payment Method harus diisi!</div>
                                </div>

                                {{-- Gambar Thumbnail --}}
                                <div class="col-md-6">
                                    <label for="add_img_thumbnail" class="form-label fw-semibold">Gambar Thumbnail</label>
                                    <input type="file" name="image" id="add_img_thumbnail" class="form-control"
                                        accept="image/*">
                                </div>

                                {{-- Dropdown type method --}}
                                <div class="col-md-6">
                                    <label for="add_type_method" class="form-label fw-semibold">Type Payment Method</label>
                                    <select name="payment_method_type_id" id="add_type_method" class="form-select" required>
                                        <option value="" disabled selected>Pilih Type Payment Method</option>
                                        @foreach ($paymentMethodTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Type Payment Method harus dipilih!</div>
                                </div>

                                {{-- Provider --}}
                                <div class="col-md-6">
                                    <label for="add_provider" class="form-label fw-semibold">Provider</label>
                                    <input type="text" name="provider" id="add_provider" class="form-control"
                                        placeholder="Masukkan provider" required value="{{ old('provider') }}">
                                    <div class="invalid-feedback">Provide harus diisi!</div>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <label for="add_is_active" class="form-label fw-semibold">Status</label>
                                    <select name="is_active" id="add_is_active" class="form-select" required>
                                        <option value="" disabled selected>Pilih Status</option>
                                        <option value="1">Aktif</option>
                                        <option value="0">Non-Aktif</option>
                                    </select>
                                    <div class="invalid-feedback">Status harus dipilih!</div>
                                </div>

                                {{-- Req Approval Code --}}
                                <div class="col-md-6">
                                    <label for="add_is_approval_code_required" class="form-label fw-semibold">Req Approval Code</label>
                                    <select name="is_approval_code_required" id="add_is_approval_code_required" class="form-select" required>
                                        <option value="" disabled selected>Pilih Req Approval Code</option>
                                        <option value="1">Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                    <div class="invalid-feedback">Req Approval Code harus dipilih!</div>
                                </div>

                                {{-- Req number card --}}
                                <div class="col-md-6">
                                    <label for="add_is_number_card_required" class="form-label fw-semibold">Req Number Card</label>
                                    <select name="is_number_card_required" id="add_is_number_card_required" class="form-select" required>
                                        <option value="" disabled selected>Pilih Req Number Card</option>
                                        <option value="1">Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                    <div class="invalid-feedback">Req Number Card harus dipilih!</div>
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
                    {{-- Pastikan form di submit dari form id="formTambahPaymentMethod" --}}
                    <button type="submit" form="formTambahPaymentMethod" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Modal Edit Payment Method --}}
    <div class="modal fade" id="modalEditPaymentMethod" tabindex="-1" aria-labelledby="modalEditPaymentMethodLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-warning py-3 px-4">
                    <h5 class="modal-title fw-semibold text-black" id="modalEditPaymentMethodLabel">
                        <i class="fas fa-user-edit me-2"></i>Edit Payment Method
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    <form id="formEditPaymentMethod" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                        @csrf
                        {{-- Field tersembunyi untuk ID Payment Method, penting untuk proses update --}}
                        <input type="hidden" name="id" id="edit_payment_method_id">

                        <div class="container-fluid">
                            <div class="row g-3">

                                {{-- Nama Payment Method --}}
                                <div class="col-md-6">
                                    <label for="edit_payment_method_name" class="form-label fw-semibold">Nama Payment Method</label>
                                    <input type="text" name="name" id="edit_payment_method_name" class="form-control"
                                        placeholder="Masukkan nama Payment Method" required>
                                    <div class="invalid-feedback">Nama Payment Method harus diisi!</div>
                                </div>

                                {{-- Gambar Payment Method --}}
                                <div class="col-md-6">
                                    <label for="edit_payment_method_image" class="form-label fw-semibold">Gambar Payment Method</label>
                                    <input type="file" name="image" id="edit_payment_method_image" class="form-control">
                                </div>

                                {{-- Type Payment Method --}}
                                <div class="col-md-6">
                                    <label for="edit_payment_method_type" class="form-label fw-semibold">Type Payment Method</label>
                                    <select name="type" id="edit_payment_method_type" class="form-select" required>
                                        <option value="" disabled>Pilih Type Payment Method</option>
                                        @foreach ($paymentMethodTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('type', $paymentMethod->payment_method_type_id ?? '') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Type Payment Method harus dipilih!</div>
                                </div>

                                {{-- Provider --}}
                                <div class="col-md-6">
                                    <label for="edit_payment_method_provider" class="form-label fw-semibold">Provider</label>
                                    <input type="text" name="provider" id="edit_payment_method_provider" class="form-control"
                                        placeholder="Masukkan provider Payment Method" required>
                                    <div class="invalid-feedback">Provider harus diisi!</div>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <label for="edit_is_active" class="form-label fw-semibold">Status</label>
                                    <select name="is_active" id="edit_is_active" class="form-select" required>
                                        <option value="" disabled>Pilih Status</option>
                                        <option value="1" {{ old('is_active', $paymentMethod->is_active ?? '') == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('is_active', $paymentMethod->is_active ?? '') == 0 ? 'selected' : '' }}>Non-Aktif</option>
                                    </select>
                                    <div class="invalid-feedback">Status harus dipilih!</div>
                                </div>

                                {{-- Req Approval Code --}}
                                <div class="col-md-6">
                                    <label for="edit_is_approval_code_required" class="form-label fw-semibold">Req Approval Code</label>
                                    <select name="is_approval_code_required" id="edit_is_approval_code_required" class="form-select" required>
                                        <option value="" disabled selected>Pilih Req Approval Code</option>
                                        <option value="1" {{ old('is_approval_code_required', $paymentMethod->is_approval_code_required ?? '') == 1 ? 'selected' : '' }}>Ya</option>
                                        <option value="0" {{ old('is_approval_code_required', $paymentMethod->is_approval_code_required ?? '') == 0 ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                    <div class="invalid-feedback">Req Approval Code harus dipilih!</div>
                                </div>

                                {{-- Req Number Card --}}
                                <div class="col-md-6">
                                    <label for="edit_is_number_card_required" class="form-label fw-semibold">Req Number Card</label>
                                    <select name="is_number_card_required" id="edit_is_number_card_required" class="form-select" required>
                                        <option value="" disabled selected>Pilih Req Number Card</option>
                                        <option value="1" {{ old('is_number_card_required', $paymentMethod->is_number_card_required ?? '') == 1 ? 'selected' : '' }}>Ya</option>
                                        <option value="0" {{ old('is_number_card_required', $paymentMethod->is_number_card_required ?? '') == 0 ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                    <div class="invalid-feedback">Req Number Card harus dipilih!</div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="formEditPaymentMethod" class="btn btn-warning text-white px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Delete --}}
    <div id="confirmDeleteModal" class="closecashier-modal">
        <div class="closecashier-modal-content">
            <h2>Hapus Data Payment Method</h2>
            <div class="closecashier-body">
                <p id="deletePaymentMethodInfo">Apakah Anda yakin ingin menghapus Data ini?</p>
            </div>
            <div class="closecashier-footer">
                <button id="btnCancelDelete" class="btn-danger">Batal</button>
                <form id="deletePaymentMethodForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-success">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Success --}}
    <div id="successModal" class="success-modal">
        <div class="success-modal-content">
            <div class="success-icon">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none" />
                    <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                </svg>
            </div>
            <h3 class="success-title">
                @if (session('action') === 'delete')
                    Data Payment Method Berhasil Dihapus!
                @elseif (session('action') === 'edit')
                    Data Payment Method Berhasil Diedit!
                @else
                    Data Payment Method Berhasil Ditambahkan!
                @endif
            </h3>
            <p class="success-message">
                @if (session('action') === 'delete')
                    Data Payment Method telah dihapus dari sistem.
                @elseif (session('action') === 'edit')
                    Data Payment Method telah berhasil diperbarui.   
                @else
                    Data Payment Method baru telah berhasil disimpan.
                @endif
            </p>
        </div>
    </div>

    <script>
        // --- Fungsi Utama untuk Membuka Modal Edit Payment Method ---
        async function openEditModal(id) {
            try {
                const response = await fetch(`/get-payment-method/${id}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const data = await response.json();
                // console.log(data);
                const form = document.getElementById('formEditPaymentMethod');

                form.action = `/edit-payment-method/${id}`;

                document.getElementById('edit_payment_method_id').value = data.id ?? '';
                document.getElementById('edit_payment_method_name').value = data.name ?? '';
                // document.getElementById('edit_payment_method_image').value = data.img_thumbnail ?? ''; // File inputs can't be set
                document.getElementById('edit_payment_method_type').value = data.payment_method_type_id ?? '';
                document.getElementById('edit_payment_method_provider').value = data.provider ?? '';
                document.getElementById('edit_is_active').value = data.is_active ?? '';
                document.getElementById('edit_is_approval_code_required').value = data.is_approval_code_required ?? '';
                document.getElementById('edit_is_number_card_required').value = data.is_number_card_required ?? '';


                const editModal = new bootstrap.Modal(document.getElementById('modalEditPaymentMethod'));
                editModal.show();
            } catch (error) {
                console.error('Error saat mengambil data Payment Method:', error);
                alert('Terjadi kesalahan saat memuat data Payment Method. Cek konsol untuk detail.');
            }
        }
        // Logic untuk modal delete (tetap seperti sebelumnya)
        const confirmModal = document.getElementById('confirmDeleteModal');
        const deletePaymentMethodsForm = document.getElementById('deletePaymentMethodsForm');
        const deletePaymentMethodsInfo = document.getElementById('deletePaymentMethodsInfo');
        const cancelBtn = document.getElementById('btnCancelDelete');

        function openConfirmModal(id, name) {
            confirmModal.style.display = 'flex';
            deletePaymentMethodsInfo.innerHTML =    
                `<p>Apakah Anda yakin ingin menghapus Data ini <strong>${name}</strong>?</p>`;
            deletePaymentMethodsForm.action = `/delete-payment-method/${id}`;
        }

        cancelBtn.addEventListener('click', () => {
            confirmModal.style.display = 'none';
        });

        // Modal success feedback
        @if (session('success'))
            window.addEventListener('load', () => {
                const successModal = document.getElementById('successModal');
                successModal.style.display = 'flex';
                setTimeout(() => {
                    successModal.style.display = 'none';
                }, 2500);
            });
        @endif

        // Function aler error Exception
        document.addEventListener('DOMContentLoaded', function() {
            function showAlert(type, message) {
                const existing = document.querySelector('.alert-slide');
                if (existing) existing.remove();

                const alertDiv = document.createElement('div');
                alertDiv.className = `alert-slide ${type}`;
                alertDiv.innerHTML = `
            <div style="font-weight:600; margin-right:.4rem;">${type === 'error' ? 'Gagal!' : 'Berhasil!'}</div>
            <div style="flex:1;">${message}</div>
            <button class="alert-close" aria-label="close">&times;</button>
        `;
                document.body.appendChild(alertDiv);

                alertDiv.querySelector('.alert-close').addEventListener('click', () => {
                    alertDiv.classList.remove('show');
                    setTimeout(() => alertDiv.remove(), 250);
                });

                // show
                setTimeout(() => alertDiv.classList.add('show'), 50);

                // auto hide
                setTimeout(() => {
                    alertDiv.classList.remove('show');
                    setTimeout(() => alertDiv.remove(), 300);
                }, 20000);
            }

            @if (session('error'))
                console.log('Session Error:', @json(session('error')));
                showAlert('error', @json(session('error')));
            @endif

            @if (session('success'))
                showAlert('success', @json(session('success')));
            @endif
        });
    </script>
@endsection
