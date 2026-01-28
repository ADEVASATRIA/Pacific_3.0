@extends('main.back_blank')
@section('title', 'Data Package Combo')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="promo-page">
        <h2 class="page-title mb-4">Data Package Combo</h2>

        {{-- Filter --}}
        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('package-combo') }}" class="filter-form flex items-end gap-4 flex-wrap">
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
                            data-bs-toggle="modal" data-bs-target="#modalTambahPackageCombo">
                            Tambah Package
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
                        <th>Nama</th>
                        <th>Weight / Urutan</th>
                        <th>Harga</th>
                        <th>Durasi</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Akhir</th>
                        <th>Extra Ticket</th>
                        <th>Tipe Khusus</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($packageCombos as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->weight }}</td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $item->expired_duration }}</td>
                            <td class="text-center">
                                @if ($item->start_date)
                                    {{ \Carbon\Carbon::parse($item->start_date)->translatedFormat('d F Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($item->end_date)
                                    {{ \Carbon\Carbon::parse($item->end_date)->translatedFormat('d F Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">{{ $item->details->sum('qty_extra') }}</td>
                            <td class="text-center">
                                @if ($item->tipe_khusus == 1)
                                    <span class="badge bg-black">Packet</span>
                                @endif
                            </td>
                            <td class="text-center">{!! $item->getBadgeHtml($item->is_active) !!}</td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="openEditModal({{ $item->id }})">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="openConfirmModal({{ $item->id }}, '{{ $item->name }}')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-gray-500 py-3">Tidak ada data tiket</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($packageCombos->hasPages())
                <div class="mt-4 flex justify-center">
                    {{ $packageCombos->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Tambah Package Combo --}}
    <div class="modal fade" id="modalTambahPackageCombo" tabindex="-1" aria-labelledby="modalTambahPackageComboLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold text-black" id="modalTambahPackageComboLabel">
                        <i class="fas fa-tags me-2"></i>Tambah Package Combo Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    <form action="{{ route('add.package-combo') }}" method="POST" id="formTambahPackageCombo"
                        class="needs-validation" novalidate>
                        @csrf

                        <div class="container-fluid">
                            <div class="row g-3">
                                {{-- Nama --}}
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">Nama Package</label>
                                    <input type="text" name="name" id="name" class="form-control shadow-sm"
                                        required>
                                </div>

                                {{-- Weight --}}
                                <div class="col-md-6">
                                    <label for="weight" class="form-label fw-semibold">Weight / Urutan</label>
                                    <input type="number" name="weight" id="weight" class="form-control shadow-sm"
                                        required>
                                </div>

                                {{-- Harga --}}
                                <div class="col-md-6">
                                    <label for="price" class="form-label fw-semibold">Harga</label>
                                    <input type="number" name="price" id="price" class="form-control shadow-sm"
                                        required>
                                </div>

                                {{-- Pilihan Mode Expired --}}
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Metode Expired</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="expired_mode"
                                                id="mode_duration" value="duration" checked>
                                            <label class="form-check-label" for="mode_duration">
                                                Durasi (Hari)
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="expired_mode"
                                                id="mode_date" value="date">
                                            <label class="form-check-label" for="mode_date">
                                                Tanggal (Start - End)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Expired Duration (Input Hari) --}}
                                <div class="col-md-6" id="durationInputGroup">
                                    <label for="expired_duration" class="form-label fw-semibold">Expired Duration
                                        (hari)</label>
                                    <input type="number" name="expired_duration" id="expired_duration"
                                        class="form-control shadow-sm">
                                </div>

                                {{-- Tanggal Start & End (Input Date) --}}
                                <div class="col-md-6" id="dateStartGroup" style="display: none;">
                                    <label for="start_date" class="form-label fw-semibold">Start Date</label>
                                    <input type="date" name="start_date" id="start_date"
                                        class="form-control shadow-sm">
                                </div>
                                <div class="col-md-6" id="dateEndGroup" style="display: none;">
                                    <label for="end_date" class="form-label fw-semibold">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control shadow-sm">
                                </div>

                                {{-- Qty --}}
                                <div class="col-md-6">
                                    <label for="tempQty" class="form-label fw-semibold">Jumlah Tiket (Qty)</label>
                                    <input type="number" name="tempQty" id="tempQty" class="form-control shadow-sm"
                                        required>
                                </div>

                                {{-- Tipe Khusus --}}
                                <div class="col-md-6">
                                    <label for="tipe_khusus" class="form-label fw-semibold">Tipe Khusus</label>
                                    <select name="tipe_khusus" id="tipe_khusus" class="form-select shadow-sm" required>
                                        <option value="1">Packet</option>
                                    </select>
                                </div>

                                {{-- Status Aktif --}}
                                <div class="col-md-6">
                                    <label for="is_active" class="form-label fw-semibold">Status Aktif</label>
                                    <select name="is_active" id="is_active" class="form-select shadow-sm" required>
                                        <option value="1" selected>Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                </div>

                                {{-- Item ID --}}
                                <div class="col-md-6">
                                    <label for="item_id" class="form-label fw-semibold">Tipe Tiket</label>
                                    <select name="item_id" id="item_id" class="form-select shadow-sm" required>
                                        <option value="1">Tiket Dewasa (*tidak ada tambahan tiket)</option>
                                        <option value="2">Tiket Anak (*ada tambahan tiket)</option>
                                    </select>
                                </div>

                                {{-- Extra Qty --}}
                                <div class="col-md-6" id="extraQtyGroup" style="display: none;">
                                    <label for="qty_extra" class="form-label fw-semibold">Tambahan Tiket (Extra
                                        Qty)</label>
                                    <input type="number" name="qty_extra" id="qty_extra" class="form-control shadow-sm"
                                        value="0">
                                </div>
                            </div>

                            {{-- Tombol Submit --}}
                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-primary shadow-sm px-4">
                                    <i class="fas fa-save me-2"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    {{-- Modal Edit Package Combo --}}
    <div class="modal fade" id="modalEditPackageCombo" tabindex="-1" aria-labelledby="modalEditPackageComboLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-warning py-3 px-4">
                    <h5 class="modal-title fw-semibold text-black" id="modalEditPackageComboLabel">
                        <i class="fas fa-tags me-2"></i>Edit Package Combo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    <form id="formEditPackageCombo" method="POST" class="needs-validation" novalidate>
                        @csrf

                        <div class="container-fluid">
                            <div class="row g-3">
                                {{-- Nama --}}
                                <div class="col-md-6">
                                    <label for="edit_name" class="form-label fw-semibold">Nama Package</label>
                                    <input type="text" name="name" id="edit_name" class="form-control shadow-sm"
                                        required>
                                </div>

                                {{-- Weight --}}
                                <div class="col-md-6">
                                    <label for="edit_weight" class="form-label fw-semibold">Weight / Urutan</label>
                                    <input type="number" name="weight" id="edit_weight" class="form-control shadow-sm"
                                        required>
                                </div>

                                {{-- Harga --}}
                                <div class="col-md-6">
                                    <label for="edit_price" class="form-label fw-semibold">Harga</label>
                                    <input type="number" name="price" id="edit_price" class="form-control shadow-sm"
                                        required>
                                </div>

                                {{-- Pilihan Mode Expired --}}
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Metode Expired</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="expired_mode"
                                                id="edit_mode_duration" value="duration" checked>
                                            <label class="form-check-label" for="edit_mode_duration">
                                                Durasi (Hari)
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="expired_mode"
                                                id="edit_mode_date" value="date">
                                            <label class="form-check-label" for="edit_mode_date">
                                                Tanggal (Start - End)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Expired Duration (Input Hari) --}}
                                <div class="col-md-6" id="edit_durationInputGroup">
                                    <label for="edit_expired_duration" class="form-label fw-semibold">Expired Duration
                                        (hari)</label>
                                    <input type="number" name="expired_duration" id="edit_expired_duration"
                                        class="form-control shadow-sm" required>
                                </div>

                                {{-- Tanggal Start & End (Input Date) --}}
                                <div class="col-md-6" id="edit_dateStartGroup" style="display: none;">
                                    <label for="edit_start_date" class="form-label fw-semibold">Start Date</label>
                                    <input type="date" name="start_date" id="edit_start_date"
                                        class="form-control shadow-sm">
                                </div>
                                <div class="col-md-6" id="edit_dateEndGroup" style="display: none;">
                                    <label for="edit_end_date" class="form-label fw-semibold">End Date</label>
                                    <input type="date" name="end_date" id="edit_end_date" class="form-control shadow-sm">
                                </div>

                                {{-- Qty --}}
                                <div class="col-md-6">
                                    <label for="edit_tempQty" class="form-label fw-semibold">Jumlah Tiket (Qty)</label>
                                    <input type="number" name="tempQty" id="edit_tempQty"
                                        class="form-control shadow-sm" required>
                                </div>

                                {{-- Tipe Khusus --}}
                                <div class="col-md-6">
                                    <label for="edit_tipe_khusus" class="form-label fw-semibold">Tipe Khusus</label>
                                    <select name="tipe_khusus" id="edit_tipe_khusus" class="form-select shadow-sm"
                                        required>
                                        <option value="1">Packet</option>
                                    </select>
                                </div>

                                {{-- Status Aktif --}}
                                <div class="col-md-6">
                                    <label for="edit_is_active" class="form-label fw-semibold">Status Aktif</label>
                                    <select name="is_active" id="edit_is_active" class="form-select shadow-sm" required>
                                        <option value="1" selected>Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                </div>

                                {{-- Item ID --}}
                                <div class="col-md-6">
                                    <label for="edit_item_id" class="form-label fw-semibold">Tipe Tiket</label>
                                    <select name="item_id" id="edit_item_id" class="form-select shadow-sm" required>
                                        <option value="1">Tiket Dewasa (*tidak ada tambahan tiket)</option>
                                        <option value="2">Tiket Anak (*ada tambahan tiket)</option>
                                    </select>
                                </div>

                                {{-- Extra Qty --}}
                                <div class="col-md-6" id="edit_extraQtyGroup" style="display: none;">
                                    <label for="edit_qty_extra" class="form-label fw-semibold">Tambahan Tiket (Extra
                                        Qty)</label>
                                    <input type="number" name="qty_extra" id="edit_qty_extra"
                                        class="form-control shadow-sm" value="0">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" form="formEditPackageCombo" class="btn btn-warning text-white px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Delete --}}
    <div id="confirmDeleteModal" class="closecashier-modal">
        <div class="closecashier-modal-content">
            <h2>Hapus Package Combo</h2>
            <div class="closecashier-body">
                <p id="deletePackageComboInfo">Apakah Anda yakin ingin menghapus Data ini?</p>
            </div>
            <div class="closecashier-footer">
                <button id="btnCancelDelete" class="btn-danger">Batal</button>
                <form id="deletePackageComboForm" method="POST" style="display:inline;">
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
                    Package Combo Berhasil Dihapus!
                @elseif (session('action') === 'edit')
                    Package Combo Berhasil Diedit!
                @else
                    Package Combo Berhasil Ditambahkan!
                @endif
            </h3>
            <p class="success-message">
                @if (session('action') === 'delete')
                    Data Package Combo telah dihapus dari sistem.
                @elseif (session('action') === 'edit')
                    Data Package Combo telah berhasil diperbarui.
                @else
                    Data Package Combo baru telah berhasil disimpan.
                @endif
            </p>

        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logic Toggle Mode Expired (Duration vs Date) untuk Modal Tambah
            const modeRadios = document.querySelectorAll('input[name="expired_mode"]');
            const durationGroup = document.getElementById('durationInputGroup');
            const dateStartGroup = document.getElementById('dateStartGroup');
            const dateEndGroup = document.getElementById('dateEndGroup');
            
            const durationInput = document.getElementById('expired_duration');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            function toggleExpiredMode() {
                const selectedMode = document.querySelector('input[name="expired_mode"]:checked').value;

                if (selectedMode === 'duration') {
                    // Show Duration, Hide Dates
                    durationGroup.style.display = 'block';
                    dateStartGroup.style.display = 'none';
                    dateEndGroup.style.display = 'none';

                    durationInput.setAttribute('required', 'required');
                    startDateInput.removeAttribute('required');
                    endDateInput.removeAttribute('required');
                    
                    // Reset values
                    startDateInput.value = '';
                    endDateInput.value = '';
                } else {
                    // Show Dates, Hide Duration
                    durationGroup.style.display = 'none';
                    dateStartGroup.style.display = 'block';
                    dateEndGroup.style.display = 'block';

                    durationInput.removeAttribute('required');
                    startDateInput.setAttribute('required', 'required');
                    endDateInput.setAttribute('required', 'required');
                    
                    // Reset value
                    durationInput.value = '';
                }
            }

            modeRadios.forEach(radio => {
                radio.addEventListener('change', toggleExpiredMode);
            });

            // Init state
            if(modeRadios.length > 0) {
                toggleExpiredMode();
            }

            // Logic untuk toggle Extra Qty pada Modal Add (jika belum ada)
            const addItemSelect = document.getElementById('item_id');
            const addExtraGroup = document.getElementById('extraQtyGroup');
            const addExtraInput = document.getElementById('qty_extra');

            if (addItemSelect && addExtraGroup) {
                function toggleAddExtraQty() {
                    if (addItemSelect.value == '2') { // Tiket Anak
                        addExtraGroup.style.display = 'block';
                        addExtraInput.value = 1; 
                    } else {
                        addExtraGroup.style.display = 'none';
                        addExtraInput.value = 0;
                    }
                }
                addItemSelect.addEventListener('change', toggleAddExtraQty);
                toggleAddExtraQty(); // Init
            }

            // Logic Toggle Mode Expired untuk Modal Edit
            const editModeRadios = document.querySelectorAll('input[name="expired_mode"][id^="edit_mode"]');
            const editDurationGroup = document.getElementById('edit_durationInputGroup');
            const editDateStartGroup = document.getElementById('edit_dateStartGroup');
            const editDateEndGroup = document.getElementById('edit_dateEndGroup');
            
            const editDurationInput = document.getElementById('edit_expired_duration');
            const editStartDateInput = document.getElementById('edit_start_date');
            const editEndDateInput = document.getElementById('edit_end_date');

            function toggleEditExpiredMode() {
                // Cari radio yang checked di dalam modal edit
                // Karena name sama dengan modal add, kita persempit scope atau cari ID spesifik
                const selectedMode = document.querySelector('input[name="expired_mode"][id^="edit_mode"]:checked').value;

                if (selectedMode === 'duration') {
                    editDurationGroup.style.display = 'block';
                    editDateStartGroup.style.display = 'none';
                    editDateEndGroup.style.display = 'none';

                    editDurationInput.setAttribute('required', 'required');
                    editStartDateInput.removeAttribute('required');
                    editEndDateInput.removeAttribute('required');
                    
                    editStartDateInput.value = '';
                    editEndDateInput.value = '';
                } else {
                    editDurationGroup.style.display = 'none';
                    editDateStartGroup.style.display = 'block';
                    editDateEndGroup.style.display = 'block';

                    editDurationInput.removeAttribute('required');
                    editStartDateInput.setAttribute('required', 'required');
                    editEndDateInput.setAttribute('required', 'required');
                    
                    editDurationInput.value = '';
                }
            }

            editModeRadios.forEach(radio => {
                radio.addEventListener('change', toggleEditExpiredMode);
            });
        });

        // Fungsi buka modal edit
        async function openEditModal(id) {
            try {
                const response = await fetch(`/get-package-combo/${id}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const data = await response.json();
                console.log('Package Combo Data:', data);

                // Pastikan form mengarah ke route update yang benar
                const form = document.getElementById('formEditPackageCombo');
                form.action = `/edit-package-combo/${id}`;

                // Isi field utama
                document.getElementById('edit_name').value = data.name ?? '';
                document.getElementById('edit_weight').value = data.weight ?? '';
                document.getElementById('edit_price').value = data.price ?? '';
                document.getElementById('edit_expired_duration').value = data.expired_duration ?? '';
                document.getElementById('edit_is_active').value = data.is_active ?? 1;
                document.getElementById('edit_tipe_khusus').value = data.tipe_khusus ?? 1;
                
                document.getElementById('edit_start_date').value = data.start_date ?? '';
                document.getElementById('edit_end_date').value = data.end_date ?? '';

                // Tentukan Mode Expired berdasarkan data
                if (data.start_date && data.end_date) {
                    document.getElementById('edit_mode_date').checked = true;
                } else {
                    document.getElementById('edit_mode_duration').checked = true;
                }
                
                // Trigger change event agar UI menyesuaikan (karena listener ada di DOMContentLoaded)
                document.getElementById('edit_mode_duration').dispatchEvent(new Event('change'));
                document.getElementById('edit_mode_date').dispatchEvent(new Event('change'));


                // Pastikan detail tersedia (bisa null kalau belum ada)
                const detail = data.details && data.details.length > 0 ? data.details[0] : null;

                document.getElementById('edit_tempQty').value = detail?.qty ?? '';
                document.getElementById('edit_item_id').value = detail?.item_id ?? 1;
                document.getElementById('edit_qty_extra').value = detail?.qty_extra ?? 0;

                // Tampilkan/hidden Extra Qty sesuai jenis tiket
                const extraGroup = document.getElementById('edit_extraQtyGroup');
                const itemSelect = document.getElementById('edit_item_id');
                const extraInput = document.getElementById('edit_qty_extra');

                function toggleExtraQtyVisibility() {
                    if (itemSelect.value == '2') {
                        // Tiket Anak → tampilkan field extra qty
                        extraGroup.style.display = 'block';
                        extraInput.required = true;
                        if (extraInput.value == '0') extraInput.value = 1;
                    } else {
                        // Tiket Dewasa → sembunyikan field extra qty
                        extraGroup.style.display = 'none';
                        extraInput.required = false;
                        extraInput.value = 0;
                    }
                }

                toggleExtraQtyVisibility();
                itemSelect.removeEventListener('change', toggleExtraQtyVisibility); // hindari multiple binding
                itemSelect.addEventListener('change', toggleExtraQtyVisibility);

                // Tampilkan modal edit
                const modal = new bootstrap.Modal(document.getElementById('modalEditPackageCombo'));
                modal.show();

            } catch (error) {
                console.error('Error:', error);
                alert('Gagal memuat data Package Combo.');
            }
        }

        // Logic untuk modal delete (tetap seperti sebelumnya)
        const confirmModal = document.getElementById('confirmDeleteModal');
        const deletePackageComboForm = document.getElementById('deletePackageComboForm');
        const deletePackageComboInfo = document.getElementById('deletePackageComboInfo');
        const cancelBtn = document.getElementById('btnCancelDelete');

        function openConfirmModal(id, name) {
            confirmModal.style.display = 'flex';
            deletePackageComboInfo.innerHTML =
                `<p>Apakah Anda yakin ingin menghapus Data ini <strong>${name}</strong>?</p>`;
            deletePackageComboForm.action = `/delete-package-combo/${id}`;
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
