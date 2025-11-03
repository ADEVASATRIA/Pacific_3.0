@extends('main.back_blank')
@section('title', 'Data Package Combo')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="promo-page">
        <h2 class="page-title">Data Package Combo</h2>

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
                        <th>Harga</th>
                        <th>Durasi</th>
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
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $item->expired_duration }}</td>
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

                                {{-- Harga --}}
                                <div class="col-md-6">
                                    <label for="price" class="form-label fw-semibold">Harga</label>
                                    <input type="number" name="price" id="price" class="form-control shadow-sm"
                                        required>
                                </div>

                                {{-- Expired Duration --}}
                                <div class="col-md-6">
                                    <label for="expired_duration" class="form-label fw-semibold">Expired Duration
                                        (hari)</label>
                                    <input type="number" name="expired_duration" id="expired_duration"
                                        class="form-control shadow-sm" required>
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
                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold" id="modalEditPackageComboLabel">
                        <i class="fas fa-tags me-2"></i>Tambah Package Combo Baru
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

                                {{-- Harga --}}
                                <div class="col-md-6">
                                    <label for="edit_price" class="form-label fw-semibold">Harga</label>
                                    <input type="number" name="price" id="edit_price" class="form-control shadow-sm"
                                        required>
                                </div>

                                {{-- Expired Duration --}}
                                <div class="col-md-6">
                                    <label for="edit_expired_duration" class="form-label fw-semibold">Expired Duration
                                        (hari)</label>
                                    <input type="number" name="expired_duration" id="edit_expired_duration"
                                        class="form-control shadow-sm" required>
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
                document.getElementById('edit_price').value = data.price ?? '';
                document.getElementById('edit_expired_duration').value = data.expired_duration ?? '';
                document.getElementById('edit_is_active').value = data.is_active ?? 1;
                document.getElementById('edit_tipe_khusus').value = data.tipe_khusus ?? 1;

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
