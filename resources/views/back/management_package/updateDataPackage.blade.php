@extends('main.back_blank')
@section('title', 'Update Data Package Customer')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="ticket-types-page">
        <h2 class="page-title mb-4">Update Data Package Customer</h2>

        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('view-update-package-home') }}"
                class="filter-form flex items-end gap-4 flex-wrap">
                <div class="form-group">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                    <input type="text" name="phone" id="phone" placeholder="Cari nomor telepon..."
                        value="{{ request('phone') }}"
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

        @if(isset($error))
            <div class="alert alert-danger mt-4">
                {{ $error }}
            </div>
        @endif

        @if(isset($customer))
            <div class="detail-info-section">
                <div class="detail-info-header">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <h3>Detail Customer</h3>
                </div>
                <div class="detail-info-content">
                    <div class="detail-info-grid">
                        <!-- Nama -->
                        <div class="detail-info-item">
                            <div class="detail-info-icon blue">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="detail-info-text">
                                <p class="detail-info-label">Nama Lengkap</p>
                                <p class="detail-info-value">{{ $customer->name }}</p>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="detail-info-item">
                            <div class="detail-info-icon green">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div class="detail-info-text">
                                <p class="detail-info-label">Nomor Telepon</p>
                                <p class="detail-info-value">{{ $customer->phone }}</p>
                            </div>
                        </div>

                        <!-- DOB -->
                        <div class="detail-info-item">
                            <div class="detail-info-icon purple">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="detail-info-text">
                                <p class="detail-info-label">Tanggal Lahir</p>
                                <p class="detail-info-value">
                                    {{ \Carbon\Carbon::parse($customer->DOB)->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($customer) && isset($viewData) && count($viewData) > 0)
            <div class="table-section mt-2 relative">
                <div class="table-wrapper">
                    <div class="table-scroll-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Package</th>
                                    <th>Tanggal Dibeli</th>
                                    <th>Jumlah Redeem</th>
                                    <th>Sisa Qty Redeem</th>
                                    <th>Tanggal Kadaluwarsa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($viewData as $data)
                                    <tr>
                                        <td>{{ $data['package_name'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($data['purchase_date'])->format('d F Y') }}</td>
                                        <td>{{ $data['total_redeemed'] }}</td>
                                        <td>{{ $data['remaining_qty'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($data['expired_date'])->format('d M Y') }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" onclick="openEditModal({{ $data['id'] }})">
                                                Edit
                                            </button>
                                            <a href="{{ route('view-detail-package', $data['id']) }}"
                                                class="btn btn-primary">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
            </div>
        </div>
    </div>
            </div>
        @elseif(isset($customer) && isset($viewData) && count($viewData) == 0)
            <p class="text-center mt-4">Customer ditemukan, tetapi tidak memiliki data package.</p>
        @elseif(!isset($phone) || $phone == '')
            <p class="text-center mt-4 text-gray-500">Masukkan nomor telepon untuk mencari data package customer.</p>
        @endif
    </div>

    <!-- Area Modal Edit -->
    <div class="modal fade" id="modalEditPackage" tabindex="-1" aria-labelledby="modalEditPackageLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-warning py-3 px-4">
                    <h5 class="modal-title fw-semibold text-black" id="modalEditPackageLabel">
                        <i class="fas fa-user-edit me-2"></i>Edit Package
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    <form id="formEditPackage" method="POST" class="needs-validation" novalidate>
                        @csrf
                        {{-- Field tersembunyi untuk ID Pelatih, penting untuk proses update --}}
                        <input type="hidden" name="id" id="edit_package_id">

                        <div class="container-fluid">
                            <div class="row g-3">

                                {{-- Nama --}}
                                <div class="col-md-6">
                                    <label for="edit_package_name" class="form-label fw-semibold">Nama Package</label>
                                    <input type="text" name="name" id="edit_package_name" class="form-control"
                                        placeholder="Masukkan nama Package" required>
                                    <div class="invalid-feedback">Nama Package harus diisi!</div>
                                </div>

                                {{-- total Redeemed --}}
                                <div class="col-md-6">
                                    <label for="edit_total_redeemed" class="form-label fw-semibold">Total Redeemed</label>
                                    <input type="text" name="total_redeemed" id="edit_total_redeemed" class="form-control"
                                        placeholder="Masukkan total redeemed" required>
                                    <div class="invalid-feedback">Total Redeemed harus diisi!</div>
                                </div>

                                {{-- remaining qty --}}
                                <div class="col-md-6">
                                    <label for="edit_remaining_qty" class="form-label fw-semibold">Remaining Qty</label>
                                    <input type="text" name="remaining_qty" id="edit_remaining_qty" class="form-control"
                                        placeholder="Masukkan remaining qty" required>
                                    <div class="invalid-feedback">Remaining Qty harus diisi!</div>
                                </div>

                                <!-- expired date -->
                                <div class="col-md-6">
                                    <label for="edit_expired_date" class="form-label fw-semibold">Expired Date</label>
                                    <input type="date" name="expired_date" id="edit_expired_date" class="form-control"
                                        placeholder="Masukkan expired date" required>
                                    <div class="invalid-feedback">Expired Date harus diisi!</div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="formEditPackage" class="btn btn-warning text-white px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Delete --}}
    <div id="confirmDeleteModal" class="closecashier-modal">
        <div class="closecashier-modal-content">
            <h2>Hapus Data Package</h2>
            <div class="closecashier-body">
                <p id="deletePackageInfo">Apakah Anda yakin ingin menghapus Data ini?</p>
            </div>
            <div class="closecashier-footer">
                <button id="btnCancelDelete" class="btn-danger">Batal</button>
                <form id="deletePackageForm" method="POST" style="display:inline;">
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
                    Data Package Berhasil Dihapus!
                @elseif (session('action') === 'edit')
                    Data Package Berhasil Diedit!
                @else
                    Data Package Berhasil Ditambahkan!
                @endif
            </h3>
            <p class="success-message">
                @if (session('action') === 'delete')
                    Data Package telah dihapus dari sistem.
                @elseif (session('action') === 'edit')
                    Data Package telah berhasil diperbarui.
                @else
                    Data Package baru telah berhasil disimpan.
                @endif
            </p>

        </div>
    </div>

    <script>
        // Fungsi untuk edit modal
        async function openEditModal(id) {
            try {
                const response = await fetch(`/get-edit-package/${id}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const data = await response.json();
                
                // Set form action
                const form = document.getElementById('formEditPackage');
                form.action = `/edit-package/${id}`;

                // Populate form dengan data yang diterima
                document.getElementById('edit_package_id').value = data.id;
                document.getElementById('edit_package_name').value = data.package_name ?? '';
                document.getElementById('edit_total_redeemed').value = data.total_redeemed ?? '';
                document.getElementById('edit_remaining_qty').value = data.remaining_qty ?? '';
                document.getElementById('edit_expired_date').value = data.expired_date ?? '';

                // Tampilkan modal
                const editModal = new bootstrap.Modal(document.getElementById('modalEditPackage'));
                editModal.show();
            } catch (error) {
                console.error('Error saat mengambil data Package:', error);
                alert('Terjadi kesalahan saat memuat data Package. Cek konsol untuk detail.');
            }
        }

        // Logic untuk modal delete (tetap seperti sebelumnya)
        const confirmModal = document.getElementById('confirmDeleteModal');
        const deletePackageForm = document.getElementById('deletePackageForm');
        const deletePackageInfo = document.getElementById('deletePackageInfo');
        const cancelBtn = document.getElementById('btnCancelDelete');

        function openConfirmModal(id, name) {
            confirmModal.style.display = 'flex';
            deletePackageInfo.innerHTML =
                `<p>Apakah Anda yakin ingin menghapus Data ini <strong>${name}</strong>?</p>`;
            deletePackageForm.action = `/delete-package/${id}`;
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