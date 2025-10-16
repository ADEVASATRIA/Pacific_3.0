@extends('main.back_blank')
@section('title', 'Data Staff')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="promo-page">
        <h2 class="page-title">Data Staff</h2>

        {{-- Filter Section (optional, currently commented out) --}}
        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('staff') }}" class="filter-form flex items-end gap-4 flex-wrap">
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
                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700"
                            data-bs-toggle="modal" data-bs-target="#modalTambahStaff">
                            Tambah Data Staff
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
                        <th class="text-center">Username</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr>
                            <td class="text-center">{{ $admin->username }}</td>
                            <td class="text-center">
                                @if ($admin->is_admin == 1)
                                    <span class="text-sm">Admin</span>
                                @elseif ($admin->is_guest == 1)
                                    <span class="text-sm">Guest</span>
                                @elseif ($admin->is_staff == 1)
                                    <span class="text-sm">Staff</span>
                                @else
                                    <span class="text-sm text-gray-500">Tidak diketahui</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {!! $admin->getBadgeHtml($admin->is_active) !!}
                            </td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm" onclick="openEditModal({{ $admin->id }})">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="openConfirmModal({{ $admin->id }}, '{{ $admin->username }}')">
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

    {{-- Modal Tambah Staff --}}
    <div class="modal fade" id="modalTambahStaff" tabindex="-1" aria-labelledby="modalTambahStaffLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold" id="modalTambahStaffLabel">
                        <i class="fas fa-user-plus me-2"></i>Tambah Staff Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    <form action="{{ route('add.staff') }}" method="POST" id="formTambahStaff" class="needs-validation"
                        novalidate>
                        @csrf

                        <div class="container-fluid">
                            <div class="row g-3">

                                {{-- Username --}}
                                <div class="col-md-6">
                                    <label for="username" class="form-label fw-semibold">Username</label>
                                    <input type="text" name="username" id="username" class="form-control"
                                        placeholder="Masukkan username" required>
                                    <div class="invalid-feedback">Username wajib diisi.</div>
                                </div>

                                {{-- Password --}}
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold">Password</label>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="Masukkan password" minlength="6" required>
                                    <div class="invalid-feedback">Password minimal 6 karakter.</div>
                                </div>


                                {{-- Tipe Akun --}}
                                <div class="col-md-6">
                                    <label for="tipe" class="form-label fw-semibold">Tipe Akun</label>
                                    <select name="tipe" id="tipe" class="form-select" required>
                                        <option value="" disabled selected>Pilih tipe akun</option>
                                        <option value="1">Staff (Wajib PIN)</option>
                                        <option value="2">Admin</option>
                                    </select>
                                    <div class="invalid-feedback">Pilih tipe akun terlebih dahulu.</div>
                                </div>

                                {{-- PIN --}}
                                <div class="col-md-6" id="pin_wrapper" style="display:none;">
                                    <label for="pin" class="form-label fw-semibold">PIN (4 Digit)</label>
                                    <input type="number" name="pin" id="pin" class="form-control"
                                        placeholder="Masukkan 4 digit PIN">
                                    <div class="invalid-feedback">PIN harus terdiri dari 4 digit angka.</div>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <label for="is_active" class="form-label fw-semibold">Status Akun</label>
                                    <select name="is_active" id="is_active" class="form-select" required>
                                        <option value="1" selected>Aktif</option>
                                        <option value="0">Nonaktif</option>
                                    </select>
                                    <div class="invalid-feedback">Pilih status akun.</div>
                                </div>

                                {{-- Tombol Submit --}}
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary shadow-sm px-4">
                                        <i class="fas fa-save me-2"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal Edit Staff --}}
    <div class="modal fade" id="modalEditStaff" tabindex="-1" aria-labelledby="modalEditStaffLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold" id="modalEditStaffLabel">
                        <i class="fas fa-user-edit me-2"></i>Edit Staff
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    <form id="formEditStaff" method="POST" class="needs-validation" novalidate>
                        @csrf
                        <div class="container-fluid">
                            <div class="row g-3">

                                {{-- Username --}}
                                <div class="col-md-6">
                                    <label for="edit_username" class="form-label fw-semibold">Username</label>
                                    <input type="text" name="username" id="edit_username" class="form-control"
                                        placeholder="Masukkan username" required>
                                    <div class="invalid-feedback">Username wajib diisi.</div>
                                </div>

                                {{-- Password --}}
                                <div class="col-md-6">
                                    <label for="edit_password" class="form-label fw-semibold">Password</label>
                                    <input type="password" name="password" id="edit_password" class="form-control"
                                        placeholder="Masukkan password baru" minlength="6" required>
                                    <div class="invalid-feedback">Password minimal 6 karakter.</div>
                                </div>

                                {{-- Tipe Akun --}}
                                <div class="col-md-6">
                                    <label for="edit_tipe" class="form-label fw-semibold">Tipe Akun</label>
                                    <select name="tipe" id="edit_tipe" class="form-select" required>
                                        <option value="" disabled selected>Pilih tipe akun</option>
                                        <option value="1">Staff (Wajib PIN)</option>
                                        <option value="2">Admin</option>
                                    </select>
                                    <div class="invalid-feedback">Pilih tipe akun terlebih dahulu.</div>
                                </div>

                                {{-- PIN --}}
                                <div class="col-md-6" id="edit_pin_wrapper" style="display:none;">
                                    <label for="edit_pin" class="form-label fw-semibold">PIN (4 Digit)</label>
                                    <input type="text" name="pin" id="edit_pin" class="form-control"
                                        placeholder="Masukkan 4 digit PIN" maxlength="4" pattern="\d{4}">
                                    <div class="invalid-feedback">PIN harus terdiri dari 4 digit angka.</div>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <label for="edit_is_active" class="form-label fw-semibold">Status Akun</label>
                                    <select name="is_active" id="edit_is_active" class="form-select" required>
                                        <option value="1" selected>Aktif</option>
                                        <option value="0">Nonaktif</option>
                                    </select>
                                    <div class="invalid-feedback">Pilih status akun.</div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="formEditStaff" class="btn btn-warning text-white px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>




    {{-- Modal Delete --}}
    <div id="confirmDeleteModal" class="closecashier-modal">
        <div class="closecashier-modal-content">
            <h2>Hapus Data Staff</h2>
            <div class="closecashier-body">
                <p id="deleteStaffInfo">Apakah Anda yakin ingin menghapus Data ini?</p>
            </div>
            <div class="closecashier-footer">
                <button id="btnCancelDelete" class="btn-danger">Batal</button>
                <form id="deleteStaffForm" method="POST" style="display:inline;">
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
                    Data Staff Berhasil Dihapus!
                @elseif (session('action') === 'edit')
                    Data Staff Berhasil Diedit!
                @else
                    Data Staff Berhasil Ditambahkan!
                @endif
            </h3>
            <p class="success-message">
                @if (session('action') === 'delete')
                    Data Staff telah dihapus dari sistem.
                @elseif (session('action') === 'edit')
                    Data Staff telah berhasil diperbarui.
                @else
                    Data Staff baru telah berhasil disimpan.
                @endif
            </p>

        </div>
    </div>

    <script>
        // --- Modal Tambah Staff: tampilkan PIN jika tipe == 1
        document.addEventListener('DOMContentLoaded', function() {
            const tipeSelectAdd = document.getElementById('tipe');
            const pinWrapperAdd = document.getElementById('pin_wrapper');
            const pinInputAdd = document.getElementById('pin');

            tipeSelectAdd.addEventListener('change', function() {
                if (this.value == '1') {
                    pinWrapperAdd.style.display = 'block';
                    pinInputAdd.setAttribute('required', 'required');
                } else {
                    pinWrapperAdd.style.display = 'none';
                    pinInputAdd.removeAttribute('required');
                    pinInputAdd.value = '';
                }
            });
        });

        // --- Modal Edit Staff ---
        async function openEditModal(id) {
            try {
                const response = await fetch(`/get-staff/${id}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();

                const form = document.getElementById('formEditStaff');
                form.action = `/edit-staff/${id}`;

                document.getElementById('edit_username').value = data.username ?? '';
                document.getElementById('edit_tipe').value = data.is_admin == 1 ? 2 : 1;
                document.getElementById('edit_is_active').value = data.is_active ?? 1;
                document.getElementById('edit_pin').value = data.pin ?? '';

                const pinWrapper = document.getElementById('edit_pin_wrapper');
                const tipeVal = document.getElementById('edit_tipe').value;
                const pinInput = document.getElementById('edit_pin'); // Dapatkan input PIN
                
                if (tipeVal == '1') {
                    pinWrapper.style.display = 'block';
                    pinInput.setAttribute('required', 'required'); // <-- TAMBAHKAN INI
                } else {
                    pinWrapper.style.display = 'none';
                    pinInput.removeAttribute('required'); // <-- TAMBAHKAN INI
                }

                const editModal = new bootstrap.Modal(document.getElementById('modalEditStaff'));
                editModal.show();
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil data staff.');
            }
        }

        // Logic tampil/sembunyi PIN saat ubah tipe akun di modal edit
        document.addEventListener('DOMContentLoaded', function() {
            const tipeSelect = document.getElementById('edit_tipe');
            const pinWrapper = document.getElementById('edit_pin_wrapper');
            const pinInput = document.getElementById('edit_pin');

            tipeSelect.addEventListener('change', function() {
                if (this.value == '1') {
                    pinWrapper.style.display = 'block';
                    pinInput.setAttribute('required', 'required');
                } else {
                    pinWrapper.style.display = 'none';
                    pinInput.removeAttribute('required');
                    pinInput.value = '';
                }
            });
        });

        // Logic untuk modal delete (tetap seperti sebelumnya)
        const confirmModal = document.getElementById('confirmDeleteModal');
        const deleteStaffForm = document.getElementById('deleteStaffForm');
        const deleteStaffInfo = document.getElementById('deleteStaffInfo');
        const cancelBtn = document.getElementById('btnCancelDelete');

        function openConfirmModal(id, username) {
            confirmModal.style.display = 'flex';
            deleteStaffInfo.innerHTML =
                `<p>Apakah Anda yakin ingin menghapus Data ini <strong>${username}</strong>?</p>`;
            deleteStaffForm.action = `/delete-staff/${id}`;
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
    </script>
@endsection
