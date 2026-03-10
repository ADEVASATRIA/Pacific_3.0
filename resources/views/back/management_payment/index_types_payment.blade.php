@extends('main.back_blank')
@section('title', 'Data Payment Types')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="payment-types-page">
        <h2 class="page-title mb-4">Data Payment Types</h2>
        <div class="filter-section mb-4">
            <div class="filter-form flex items-end gap-4 flex-wrap">
                <div class="form-group">
                    <div class="flex items-center gap-2">
                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700"
                            data-bs-toggle="modal" data-bs-target="#modalTambahPaymentTypes">
                            Tambah Payment Types
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
                                <th class="text-left">Nama Payment Types</th>
                                <th class="text-left">Slug</th>
                                <th class="text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($paymentTypes as $paymentType)
                                <tr>
                                    <td>{{ $paymentType->name }}</td>
                                    <td class="text-left">{{ $paymentType->slug }}</td>
                                    <td class="text-left">
                                        <button class="btn btn-primary btn-sm"
                                            onclick="openEditModal({{ $paymentType->id }})">
                                            Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="openConfirmModal({{ $paymentType->id }}, '{{ $paymentType->name }}')">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data payment types</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    {{-- Modal Add Payment Types --}}
    <div class="modal fade" id="modalTambahPaymentTypes" tabindex="-1" aria-labelledby="modalTambahPaymentTypesLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold text-black" id="modalTambahPaymentTypesLabel">
                        <i class="fas fa-tags me-2"></i> Tambah Payment Types Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    {{-- Ganti formTambahPromo ke formTambahPaymentTypes --}}
                    <form action="{{ route('add.payment-types') }}" method="POST" id="formTambahPaymentTypes"
                        class="needs-validation" novalidate>
                        @csrf

                        <div class="container-fluid">
                            <div class="row g-4">

                                {{-- Nama Payment Types --}}
                                <div class="col-md-6">
                                    <label for="add_name" class="form-label fw-semibold">Nama Payment Types</label>
                                    <input type="text" name="name" id="add_name" class="form-control"
                                        placeholder="Masukkan nama Payment Types" required value="{{ old('name') }}">
                                    <div class="invalid-feedback">Nama Payment Types harus diisi!</div>
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
                    {{-- Pastikan form di submit dari form id="formTambahPaymentTypes" --}}
                    <button type="submit" form="formTambahPaymentTypes" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Edit Payment Types --}}
    <div class="modal fade" id="modalEditPaymentTypes" tabindex="-1" aria-labelledby="modalEditPaymentTypesLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-warning py-3 px-4">
                    <h5 class="modal-title fw-semibold text-black" id="modalEditPaymentTypesLabel">
                        <i class="fas fa-user-edit me-2"></i>Edit Payment Types
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    <form id="formEditPaymentTypes" method="POST" class="needs-validation" novalidate>
                        @csrf
                        {{-- Field tersembunyi untuk ID Payment Types, penting untuk proses update --}}
                        <input type="hidden" name="id" id="edit_payment_types_id">

                        <div class="container-fluid">
                            <div class="row g-3">

                                {{-- Nama Payment Types --}}
                                <div class="col-md-6">
                                    <label for="edit_payment_types_name" class="form-label fw-semibold">Nama Payment
                                        Types</label>
                                    <input type="text" name="name" id="edit_payment_types_name" class="form-control"
                                        placeholder="Masukkan nama Payment Types" required>
                                    <div class="invalid-feedback">Nama Payment Types harus diisi!</div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="formEditPaymentTypes" class="btn btn-warning text-white px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Delete --}}
    <div id="confirmDeleteModal" class="closecashier-modal">
        <div class="closecashier-modal-content">
            <h2>Hapus Data Payment Types</h2>
            <div class="closecashier-body">
                <p id="deletePaymentTypesInfo">Apakah Anda yakin ingin menghapus Data ini?</p>
            </div>
            <div class="closecashier-footer">
                <button id="btnCancelDelete" class="btn-danger">Batal</button>
                <form id="deletePaymentTypesForm" method="POST" style="display:inline;">
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
                    Data Payment Types Berhasil Dihapus!
                @elseif (session('action') === 'edit')
                    Data Payment Types Berhasil Diedit!
                @else
                    Data Payment Types Berhasil Ditambahkan!
                @endif
            </h3>
            <p class="success-message">
                @if (session('action') === 'delete')
                    Data Payment Types telah dihapus dari sistem.
                @elseif (session('action') === 'edit')
                    Data Payment Types telah berhasil diperbarui.
                @else
                    Data Payment Types baru telah berhasil disimpan.
                @endif
            </p>
        </div>
    </div>


    <script>
        // --- Fungsi Utama untuk Membuka Modal Edit Clubhouse ---
        async function openEditModal(id) {
            try {
                const response = await fetch(`/get-payment-types/${id}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const data = await response.json();
                // console.log(data);
                const form = document.getElementById('formEditPaymentTypes');

                form.action = `/edit-payment-type/${id}`;

                document.getElementById('edit_payment_types_id').value = data.id ?? '';
                document.getElementById('edit_payment_types_name').value = data.name ?? '';

                const editModal = new bootstrap.Modal(document.getElementById('modalEditPaymentTypes'));
                editModal.show();
            } catch (error) {
                console.error('Error saat mengambil data Payment Types:', error);
                alert('Terjadi kesalahan saat memuat data Payment Types. Cek konsol untuk detail.');
            }
        }
        // Logic untuk modal delete (tetap seperti sebelumnya)
        const confirmModal = document.getElementById('confirmDeleteModal');
        const deletePaymentTypesForm = document.getElementById('deletePaymentTypesForm');
        const deletePaymentTypesInfo = document.getElementById('deletePaymentTypesInfo');
        const cancelBtn = document.getElementById('btnCancelDelete');

        function openConfirmModal(id, name) {
            confirmModal.style.display = 'flex';
            deletePaymentTypesInfo.innerHTML =
                `<p>Apakah Anda yakin ingin menghapus Data ini <strong>${name}</strong>?</p>`;
            deletePaymentTypesForm.action = `/delete-payment-type/${id}`;
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
