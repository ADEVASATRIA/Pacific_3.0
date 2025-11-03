@extends('main.back_blank')
@section('title', 'Data pelatih')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="promo-page">
        <h2 class="page-title">Data Pelatih</h2>
        <div class="filter-section mb-4">
            <div class="filter-form flex items-end gap-4 flex-wrap">
                <div class="form-group">
                    <div class="flex items-center gap-2">
                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700"
                            data-bs-toggle="modal" data-bs-target="#modalTambahCoach">
                            Tambah Pelatih
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-section mt-4">
            <table class="table w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center">Nama</th>
                        <th class="text-center">Nomer Telephone</th>
                        <th class="text-center">Awal Masa Berlaku</th>
                        <th class="text-center">Akhir masa Berlaku</th>
                        <th class="text-center">Clubhouse</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($coaches as $coach)
                        <tr>
                            <td>{{ $coach->name }}</td>
                            <td>{{ $coach->phone }}</td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($coach->awal_masa_berlaku)->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($coach->akhir_masa_berlaku)->format('d M Y') }}
                            </td>
                            <td>{{ $coach->clubhouse->name ?? ($coach->clubhouse2->name ?? '-') }}</td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm" onclick="openEditModal({{ $coach->id }})">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="openConfirmModal({{ $coach->id }}, '{{ $coach->name }}')">
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
            @if ($coaches->hasPages())
                <div class="mt-4 flex justify-center">
                    {{ $coaches->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Add Pelatih --}}
    <div class="modal fade" id="modalTambahCoach" tabindex="-1" aria-labelledby="modalTambahCoachLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold text-black" id="modalTambahCoachLabel">
                        <i class="fas fa-tags me-2"></i> Tambah Coach Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    {{-- Ganti formTambahPromo ke formTambahCoach --}}
                    <form action="{{ route('add.coach') }}" method="POST" id="formTambahCoach" class="needs-validation"
                        novalidate>
                        @csrf

                        <div class="container-fluid">
                            <div class="row g-4">

                                {{-- Nama --}}
                                <div class="col-md-6">
                                    <label for="add_name" class="form-label fw-semibold">Nama Pelatih</label>
                                    <input type="text" name="name" id="add_name" class="form-control"
                                        placeholder="Masukkan nama pelatih" required value="{{ old('name') }}">
                                    <div class="invalid-feedback">Nama pelatih harus diisi!</div>
                                </div>

                                {{-- Nomor Telepon --}}
                                <div class="col-md-6">
                                    <label for="add_phone" class="form-label fw-semibold">Nomor Telepon</label>
                                    <input type="text" name="phone" id="add_phone" class="form-control"
                                        placeholder="Masukkan nomor telepon" required value="{{ old('phone') }}">
                                    <div class="invalid-feedback">Nomor Telepon pelatih harus diisi!</div>
                                </div>

                                {{-- Awal Masa Berlaku --}}
                                <div class="col-md-6">
                                    <label for="add_awal_masa_berlaku" class="form-label fw-semibold">Awal Masa
                                        Berlaku</label>
                                    <input type="date" name="awal_masa_berlaku" id="add_awal_masa_berlaku"
                                        class="form-control" required value="{{ old('awal_masa_berlaku') }}">
                                    <div class="invalid-feedback">Awal Masa Berlaku harus diisi!</div>
                                </div>

                                {{-- Akhir Masa Berlaku --}}
                                <div class="col-md-6">
                                    <label for="add_akhir_masa_berlaku" class="form-label fw-semibold">Akhir Masa
                                        Berlaku</label>
                                    <input type="date" name="akhir_masa_berlaku" id="add_akhir_masa_berlaku"
                                        class="form-control" required value="{{ old('akhir_masa_berlaku') }}">
                                    <div class="invalid-feedback">Akhir Masa Berlaku harus diisi!</div>
                                </div>

                                {{-- Clubhouse --}}
                                <div class="col-md-6">
                                    <label for="add_clubhouse_id" class="form-label fw-semibold">Clubhouse</label>
                                    <select class="form-select" aria-label="Clubhouse" id="add_clubhouse_id"
                                        name="clubhouse_id" required>
                                        <option value="" disabled selected>Pilih Clubhouse</option>
                                        {{-- Loop ini bergantung pada variabel $clubhouses yang disediakan dari Controller --}}
                                        @if (isset($clubhouse))
                                            @foreach ($clubhouse as $cb)
                                                <option value="{{ $cb->id }}"
                                                    {{ old('clubhouse_id') == $cb->id ? 'selected' : '' }}>
                                                    {{ $cb->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="invalid-feedback">Clubhouse harus dipilih!</div>
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
                    <button type="submit" form="formTambahCoach" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>

            </div>
        </div>
    </div>


    {{-- Modal Edit Pelatih --}}
    <div class="modal fade" id="modalEditCoach" tabindex="-1" aria-labelledby="modalEditCoachLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold" id="modalEditCoachLabel">
                        <i class="fas fa-user-edit me-2"></i>Edit Coach
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    <form id="formEditCoach" method="POST" class="needs-validation" novalidate>
                        @csrf
                        {{-- Field tersembunyi untuk ID Pelatih, penting untuk proses update --}}
                        <input type="hidden" name="id" id="edit_coach_id">

                        <div class="container-fluid">
                            <div class="row g-3">

                                {{-- Nama --}}
                                <div class="col-md-6">
                                    <label for="edit_coach_name" class="form-label fw-semibold">Nama Pelatih</label>
                                    <input type="text" name="name" id="edit_coach_name" class="form-control"
                                        placeholder="Masukkan nama pelatih" required>
                                    <div class="invalid-feedback">Nama pelatih harus diisi!</div>
                                </div>

                                {{-- Nomor Telepon --}}
                                <div class="col-md-6">
                                    <label for="edit_coach_phone" class="form-label fw-semibold">Nomor Telepon</label>
                                    <input type="text" name="phone" id="edit_coach_phone" class="form-control"
                                        placeholder="Masukkan nomor telepon" required>
                                    <div class="invalid-feedback">Nomor Telepon pelatih harus diisi!</div>
                                </div>

                                {{-- Awal Masa Berlaku --}}
                                <div class="col-md-6">
                                    <label for="edit_coach_awal_masa_berlaku" class="form-label fw-semibold">Awal Masa
                                        Berlaku</label>
                                    <input type="date" name="awal_masa_berlaku" id="edit_coach_awal_masa_berlaku"
                                        class="form-control" required>
                                    <div class="invalid-feedback">Awal Masa Berlaku harus diisi!</div>
                                </div>

                                {{-- Akhir Masa Berlaku --}}
                                <div class="col-md-6">
                                    <label for="edit_coach_akhir_masa_berlaku" class="form-label fw-semibold">Akhir Masa
                                        Berlaku</label>
                                    <input type="date" name="akhir_masa_berlaku" id="edit_coach_akhir_masa_berlaku"
                                        class="form-control" required>
                                    <div class="invalid-feedback">Akhir Masa Berlaku harus diisi!</div>
                                </div>

                                {{-- Clubhouse --}}
                                <div class="col-md-6">
                                    <label for="edit_coach_clubhouse_id" class="form-label fw-semibold">Clubhouse</label>
                                    {{-- Konten dropdown akan diisi oleh JavaScript dari data API --}}
                                    <select class="form-select" aria-label="Clubhouse" id="edit_coach_clubhouse_id"
                                        name="clubhouse_id" required>
                                        <option value="" disabled selected>Memuat...</option>
                                    </select>
                                    <div class="invalid-feedback">Clubhouse harus dipilih!</div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="formEditCoach" class="btn btn-warning text-white px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Delete --}}
    <div id="confirmDeleteModal" class="closecashier-modal">
        <div class="closecashier-modal-content">
            <h2>Hapus Data Coach</h2>
            <div class="closecashier-body">
                <p id="deleteCoachInfo">Apakah Anda yakin ingin menghapus Data ini?</p>
            </div>
            <div class="closecashier-footer">
                <button id="btnCancelDelete" class="btn-danger">Batal</button>
                <form id="deleteCoachForm" method="POST" style="display:inline;">
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
                    Data Coach Berhasil Dihapus!
                @elseif (session('action') === 'edit')
                    Data Coach Berhasil Diedit!
                @else
                    Data Coach Berhasil Ditambahkan!
                @endif
            </h3>
            <p class="success-message">
                @if (session('action') === 'delete')
                    Data Coach telah dihapus dari sistem.
                @elseif (session('action') === 'edit')
                    Data Coach telah berhasil diperbarui.
                @else
                    Data Coach baru telah berhasil disimpan.
                @endif
            </p>

        </div>
    </div>


    <script>
        // --- Fungsi Helper untuk Format Tanggal (YYYY-MM-DD) ---
        const formatDate = (dateString) => {
            if (!dateString) return '';

            // Ambil hanya bagian tanggal jika ada timestamp (misal: '2023-10-15 00:00:00')
            if (dateString.length > 10) {
                dateString = dateString.substring(0, 10);
            }

            // Cek apakah string sudah dalam format YYYY-MM-DD
            if (dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
                return dateString;
            }

            // Fallback parsing (kurang disarankan, tapi untuk jaga-jaga)
            const date = new Date(dateString);

            if (isNaN(date.getTime())) {
                console.error("Tanggal tidak valid:", dateString);
                return '';
            }

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        };

        // --- Fungsi Utama untuk Membuka Modal Edit Coach ---
        async function openEditModal(id) {
            try {
                // 1. Ambil data dari endpoint
                const response = await fetch(`/get-coach/${id}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                // Data berisi { coach: {...}, clubhouse: [...] }
                const {
                    coach,
                    clubhouse
                } = await response.json();

                const form = document.getElementById('formEditCoach');
                const selectClubhouse = document.getElementById('edit_coach_clubhouse_id');

                // 2. Set Action URL dan ID tersembunyi
                form.action = `/edit-coach/${id}`;
                document.getElementById('edit_coach_id').value = coach.id;

                // 3. Isi field input teks/tanggal
                document.getElementById('edit_coach_name').value = coach.name ?? '';
                document.getElementById('edit_coach_phone').value = coach.phone ?? '';

                // Mengisi tanggal dengan fungsi helper yang telah diperbaiki
                document.getElementById('edit_coach_awal_masa_berlaku').value = formatDate(coach.awal_masa_berlaku);
                document.getElementById('edit_coach_akhir_masa_berlaku').value = formatDate(coach.akhir_masa_berlaku);

                // 4. Isi dropdown Clubhouse
                selectClubhouse.innerHTML = ''; // Kosongkan opsi lama

                let optionsHtml = '<option value="" disabled>Pilih Clubhouse</option>';

                // Loop melalui data clubhouse yang dikirim dari controller
                clubhouse.forEach(cb => {
                    const isSelected = cb.id === coach.clubhouse_id || cb.id === coach.id_club_renang;
                    optionsHtml +=
                        `<option value="${cb.id}" ${isSelected ? 'selected' : ''}>${cb.name}</option>`;
                });

                selectClubhouse.innerHTML = optionsHtml;

                // 5. Tampilkan modal
                const editModal = new bootstrap.Modal(document.getElementById('modalEditCoach'));
                editModal.show();
            } catch (error) {
                console.error('Error saat mengambil data Coach:', error);
                // Ganti alert() dengan custom UI notification jika tersedia
                alert('Terjadi kesalahan saat memuat data pelatih. Cek konsol untuk detail.');
            }
        }
        // Logic untuk modal delete (tetap seperti sebelumnya)
        const confirmModal = document.getElementById('confirmDeleteModal');
        const deleteCoachForm = document.getElementById('deleteCoachForm');
        const deleteCoachInfo = document.getElementById('deleteCoachInfo');
        const cancelBtn = document.getElementById('btnCancelDelete');

        function openConfirmModal(id, name) {
            confirmModal.style.display = 'flex';
            deleteCoachInfo.innerHTML =
                `<p>Apakah Anda yakin ingin menghapus Data ini <strong>${name}</strong>?</p>`;
            deleteCoachForm.action = `/delete-coach/${id}`;
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
