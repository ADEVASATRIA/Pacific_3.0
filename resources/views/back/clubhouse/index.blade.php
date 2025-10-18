@extends('main.back-office')
@section('title', 'Data Clubhouse')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="promo-page">
        <h2 class="page-title">Data Clubhouse</h2>
        <div class="filter-section mb-4">
            <div class="filter-form flex items-end gap-4 flex-wrap">
                <div class="form-group">
                    <div class="flex items-center gap-2">
                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700"
                            data-bs-toggle="modal" data-bs-target="#modalTambahClubhouse">
                            Tambah Clubhouse
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-section mt-4">
            <table class="table w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center">Nama Clubhouse</th>
                        <th class="text-center">Lokasi</th>
                        <th class="text-center">Nomer Telephone</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clubhouses as $clubhouse)
                        <tr>
                            <td>{{ $clubhouse->name }}</td>
                            <td>{{ $clubhouse->location }}</td>
                            <td>{{ $clubhouse->phone }}</td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm" onclick="openEditModal({{ $clubhouse->id }})">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="openConfirmModal({{ $clubhouse->id }}, '{{ $clubhouse->name }}')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 py-3">
                                Tidak ada data Pelatih
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($clubhouses->hasPages())
                <div class="mt-4 flex justify-center">
                    {{ $clubhouses->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Add Clubhouse --}}
    <div class="modal fade" id="modalTambahClubhouse" tabindex="-1" aria-labelledby="modalTambahClubhouseLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold" id="modalTambahClubhouseLabel">
                        <i class="fas fa-tags me-2"></i> Tambah Clubhouse Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    {{-- Ganti formTambahPromo ke formTambahCoach --}}
                    <form action="{{ route('add.clubhouse') }}" method="POST" id="formTambahClubhouse" class="needs-validation"
                        novalidate>
                        @csrf

                        <div class="container-fluid">
                            <div class="row g-4">

                                {{-- Nama --}}
                                <div class="col-md-6">
                                    <label for="add_name" class="form-label fw-semibold">Nama Clubhouse</label>
                                    <input type="text" name="name" id="add_name" class="form-control"
                                        placeholder="Masukkan nama Clubhouse" required value="{{ old('name') }}">
                                    <div class="invalid-feedback">Nama Clubhouse harus diisi!</div>
                                </div>

                                {{-- Lokasi --}}
                                <div class="col-md-6">
                                    <label for="add_location" class="form-label fw-semibold">Lokasi</label>
                                    <input type="text" name="location" id="add_location" class="form-control"
                                        placeholder="Masukkan lokasi Clubhouse" required value="{{ old('location') }}">
                                    <div class="invalid-feedback">Lokasi Clubhouse harus diisi!</div>
                                </div>


                                {{-- Nomor Telepon --}}
                                <div class="col-md-6">
                                    <label for="add_phone" class="form-label fw-semibold">Nomor Telepon</label>
                                    <input type="text" name="phone" id="add_phone" class="form-control"
                                        placeholder="Masukkan nomor telepon" required value="{{ old('phone') }}">
                                    <div class="invalid-feedback">Nomor Telepon pelatih harus diisi!</div>
                                </div>

                                {{-- Placeholder untuk kolom kosong agar layout tetap rapi --}}
                                <div class="col-md-6">
                                    {{-- Kosong --}}
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
                    {{-- Pastikan form di submit dari form id="formTambahCoach" --}}
                    <button type="submit" form="formTambahClubhouse" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>

            </div>
        </div>
    </div>


    {{-- Modal Edit Clubhouse --}}
    <div class="modal fade" id="modalEditClubhouse" tabindex="-1" aria-labelledby="modalEditClubhouseLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold" id="modalEditClubhouseLabel">
                        <i class="fas fa-user-edit me-2"></i>Edit Clubhouse
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    <form id="formEditClubhouse" method="POST" class="needs-validation" novalidate>
                        @csrf
                        {{-- Field tersembunyi untuk ID Pelatih, penting untuk proses update --}}
                        <input type="hidden" name="id" id="edit_coach_id">

                        <div class="container-fluid">
                            <div class="row g-3">

                                {{-- Nama --}}
                                <div class="col-md-6">
                                    <label for="edit_clubhouse_name" class="form-label fw-semibold">Nama Clubhouse</label>
                                    <input type="text" name="name" id="edit_clubhouse_name" class="form-control"
                                        placeholder="Masukkan nama Clubhouse" required>
                                    <div class="invalid-feedback">Nama Clubhouse harus diisi!</div>
                                </div>

                                {{-- Lokasi --}}
                                <div class="col-md-6">
                                    <label for="edit_clubhouse_location" class="form-label fw-semibold">Lokasi</label>
                                    <input type="text" name="location" id="edit_clubhouse_location" class="form-control"
                                        placeholder="Masukkan lokasi Clubhouse" required>
                                    <div class="invalid-feedback">Lokasi Clubhouse harus diisi!</div>
                                </div>

                                {{-- Nomor Telepon --}}
                                <div class="col-md-6">
                                    <label for="edit_clubhouse_phone" class="form-label fw-semibold">Nomor Telepon</label>
                                    <input type="text" name="phone" id="edit_clubhouse_phone" class="form-control"
                                        placeholder="Masukkan nomor telepon" required>
                                    <div class="invalid-feedback">Nomor Telepon Clubhouse harus diisi!</div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="formEditClubhouse" class="btn btn-warning text-white px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Delete --}}
    <div id="confirmDeleteModal" class="closecashier-modal">
        <div class="closecashier-modal-content">
            <h2>Hapus Data Clubhouse</h2>
            <div class="closecashier-body">
                <p id="deleteClubhouseInfo">Apakah Anda yakin ingin menghapus Data ini?</p>
            </div>
            <div class="closecashier-footer">
                <button id="btnCancelDelete" class="btn-danger">Batal</button>
                <form id="deleteClubhouseForm" method="POST" style="display:inline;">
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
                    Data Clubhouse Berhasil Dihapus!
                @elseif (session('action') === 'edit')
                    Data Clubhouse Berhasil Diedit!
                @else
                    Data Clubhouse Berhasil Ditambahkan!
                @endif
            </h3>
            <p class="success-message">
                @if (session('action') === 'delete')
                    Data Clubhouse telah dihapus dari sistem.
                @elseif (session('action') === 'edit')
                    Data Clubhouse telah berhasil diperbarui.
                @else
                    Data Clubhouse baru telah berhasil disimpan.
                @endif
            </p>

        </div>
    </div>


    <script>
        // --- Fungsi Utama untuk Membuka Modal Edit Clubhouse ---
        async function openEditModal(id) {
            try {
                const response = await fetch(`/get-clubhouse/${id}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const data = await response.json();
                // console.log(data);
                const form = document.getElementById('formEditClubhouse');
                
                form.action = `/edit-clubhouse/${id}`;

                document.getElementById('edit_clubhouse_name').value = data.name ?? '';
                document.getElementById('edit_clubhouse_location').value = data.location ?? '';
                document.getElementById('edit_clubhouse_phone').value = data.phone ?? '';
            
                const editModal = new bootstrap.Modal(document.getElementById('modalEditClubhouse'));
                editModal.show();
            } catch (error) {
                console.error('Error saat mengambil data Coach:', error);
                alert('Terjadi kesalahan saat memuat data Clubhouse. Cek konsol untuk detail.');
            }
        }
        // Logic untuk modal delete (tetap seperti sebelumnya)
        const confirmModal = document.getElementById('confirmDeleteModal');
        const deleteClubhouseForm = document.getElementById('deleteClubhouseForm');
        const deleteClubhouseInfo = document.getElementById('deleteClubhouseInfo');
        const cancelBtn = document.getElementById('btnCancelDelete');

        function openConfirmModal(id, name) {
            confirmModal.style.display = 'flex';
            deleteClubhouseInfo.innerHTML =
                `<p>Apakah Anda yakin ingin menghapus Data ini <strong>${name}</strong>?</p>`;
            deleteClubhouseForm.action = `/delete-clubhouse/${id}`;
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
