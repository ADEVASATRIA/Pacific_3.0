@extends('main.back_blank')
@section('title', 'Data Voucher')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="voucher-page">
        <h2 class="page-title mb-4">Data Voucher</h2>
        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('voucher') }}" class="filter-form flex items-end gap-4 flex-wrap">

                {{-- Filter nama --}}
                <div class="form-group">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Voucher</label>
                    <input type="text" name="name" id="name" value="{{ request('name') }}"
                        class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="masukkan nama voucher..">
                </div>

                {{-- Filter Start Date --}}
                <div class="form-group">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                        class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                {{-- Filter End Date --}}
                <div class="form-group">
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                        class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm">
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
                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700"
                            data-bs-toggle="modal" data-bs-target="#modalTambahVoucher">
                            Tambah Voucher
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
                                <th class="text-center">Nama Voucher</th>
                                <th class="text-center">Value</th>
                                <th class="text-center">Quota</th>
                                <th class="text-center">Start Date</th>
                                <th class="text-center">End Data</th>
                                <th class="text-center">Min Purchase</th>
                                <th class="text-center">Max Discount</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vouchers as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td class="text-center">{{ $item->value }}</td>
                                    <td class="text-center">{{ $item->quota }}</td>
                                    <td class="text-center">{{ $item->start_date }}</td>
                                    <td class="text-center">{{ $item->end_date }}</td>
                                    <td class="text-center">{{ $item->min_purchase }}</td>
                                    <td class="text-center">{{ $item->max_discount }}</td>
                                    <td class="text-center">{!! $item->getBadgeHtml($item->is_active) !!}</td>
                                    <td class="text-center">
                                        <button class="btn btn-primary btn-sm"
                                            onclick="openEditModal({{ $item->id }})">
                                            Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="openConfirmModal({{ $item->id }}, '{{ $item->name }}')">
                                            Delete
                                        </button>
                                        <button type="button" class="row-edit btn btn-secondary btn-sm"
                                            onclick="window.location.href='{{ route('voucher.log', $item->id) }}'">
                                            Log
                                        </button>
                                    </td>
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

    {{-- Modal Add Voucher --}}
    <div class="modal fade" id="modalTambahVoucher" tabindex="-1" aria-labelledby="modalTambahVoucherLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold text-black" id="modalTambahVoucherLabel">
                        <i class="fas fa-tags me-2"></i> Tambah Voucher Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    <form action="{{ route('add.voucher') }}" method="POST" id="formTambahVoucher"
                        class="needs-validation" novalidate>
                        @csrf

                        <div class="container-fluid">
                            <div class="row g-4">

                                {{-- Nama Voucher --}}
                                <div class="col-md-6">
                                    <label for="add_name" class="form-label fw-semibold">Nama Voucher<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="add_name" class="form-control"
                                        placeholder="Masukkan nama Voucher" required value="{{ old('name') }}">
                                    <div class="invalid-feedback">Nama Voucher harus diisi!</div>
                                </div>

                                {{-- Requirement --}}
                                <div class="col-md-6">
                                    <label for="add_requirements" class="form-label fw-semibold">Requirement<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="requirements" id="add_requirements" class="form-control"
                                        placeholder="Masukkan requirement" required value="{{ old('requirements') }}">
                                    <div class="invalid-feedback">Requirement harus diisi!</div>
                                </div>

                                <!-- type_voucher -->
                                <div class="col-md-6">
                                    <label for="add_type_voucher" class="form-label fw-semibold">Type Voucher<span
                                            class="text-danger">*</span></label>
                                    <select name="type_voucher" id="add_type_voucher" class="form-select"
                                        required>
                                        <option value="">Pilih Type Voucher</option>
                                        <option value="fixed" {{ old('type_voucher') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                        <option value="percent" {{ old('type_voucher') == 'percent' ? 'selected' : '' }}>Percent</option>
                                    </select>
                                    <div class="invalid-feedback">Type Voucher harus diisi!</div>
                                </div>

                                {{-- Value --}}
                                <div class="col-md-6">
                                    <label for="add_value_display" class="form-label fw-semibold">Value<span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="add_value_display" class="form-control"
                                        placeholder="Masukkan value" required value="{{ old('value') }}">
                                    <input type="hidden" name="value" id="add_value" value="{{ old('value') }}">
                                    <div class="invalid-feedback">Value harus diisi!</div>
                                </div>

                                {{-- Quota --}}
                                <div class="col-md-6">
                                    <label for="add_quota" class="form-label fw-semibold">Quota<span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="quota" id="add_quota" class="form-control"
                                        placeholder="Masukkan quota" required value="{{ old('quota') }}">
                                    <div class="invalid-feedback">Quota harus diisi!</div>
                                </div>

                                {{-- Start Date --}}
                                <div class="col-md-6">
                                    <label for="add_start_date" class="form-label fw-semibold">Start Date<span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="add_start_date" class="form-control"
                                        placeholder="Masukkan start date" required value="{{ old('start_date') }}">
                                    <div class="invalid-feedback">Start date harus diisi!</div>
                                </div>

                                {{-- End Date --}}
                                <div class="col-md-6">
                                    <label for="add_end_date" class="form-label fw-semibold">End Date<span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="add_end_date" class="form-control"
                                        placeholder="Masukkan end date" required value="{{ old('end_date') }}">
                                    <div class="invalid-feedback">End date harus diisi!</div>
                                </div>

                                {{-- Min Purchase --}}
                                <div class="col-md-6">
                                    <label for="add_min_purchase_display" class="form-label fw-semibold">Min Purchase<span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="add_min_purchase_display" class="form-control"
                                        placeholder="Masukkan min purchase" required value="{{ old('min_purchase') }}">
                                    <input type="hidden" name="min_purchase" id="add_min_purchase" value="{{ old('min_purchase') }}">
                                    <div class="invalid-feedback">Min purchase harus diisi!</div>
                                </div>

                                {{-- Max Discount --}}
                                <div class="col-md-6">
                                    <label for="add_max_discount_display" class="form-label fw-semibold">Max Discount<span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="add_max_discount_display" class="form-control"
                                        placeholder="Masukkan max discount" required value="{{ old('max_discount') }}">
                                    <input type="hidden" name="max_discount" id="add_max_discount" value="{{ old('max_discount') }}">
                                    <div class="invalid-feedback">Max discount harus diisi!</div>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <label for="add_is_active" class="form-label fw-semibold">Status<span
                                            class="text-danger">*</span></label>
                                    <select name="is_active" id="add_is_active" class="form-control" required>
                                        <option value="">Pilih Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <div class="invalid-feedback">Status harus diisi!</div>
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
                    <button type="submit" form="formTambahVoucher" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Voucher --}}
    <div class="modal fade" id="modalEditVoucher" tabindex="-1" aria-labelledby="modalEditVoucherLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-warning py-3 px-4">
                    <h5 class="modal-title fw-semibold text-black" id="modalEditVoucherLabel">
                        <i class="fas fa-user-edit me-2"></i>Edit Voucher
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    <form id="formEditVoucher" method="POST" class="needs-validation" novalidate>
                        @csrf
                        {{-- Field tersembunyi untuk ID Payment Types, penting untuk proses update --}}
                        <input type="hidden" name="id" id="edit_voucher_id">

                        <div class="container-fluid">
                            <div class="row g-3">

                                {{-- Nama Voucher --}}
                                <div class="col-md-6">
                                    <label for="edit_name" class="form-label fw-semibold">Nama Voucher</label>
                                    <input type="text" name="name" id="edit_name" class="form-control"
                                        placeholder="Masukkan nama Voucher" required>
                                    <div class="invalid-feedback">Nama Voucher harus diisi!</div>
                                </div>

                                {{-- Requirement --}}
                                <div class="col-md-6">
                                    <label for="edit_requirements" class="form-label fw-semibold">Requirement</label>
                                    <input type="text" name="requirements" id="edit_requirements" class="form-control"
                                        placeholder="Masukkan requirement" required>
                                    <div class="invalid-feedback">Requirement harus diisi!</div>
                                </div>

                                <!-- type_voucher -->
                                <div class="col-md-6">
                                    <label for="edit_type_voucher" class="form-label fw-semibold">Type Voucher</label>
                                    <select name="type_voucher" id="edit_type_voucher" class="form-select"
                                        required>
                                        <option value="">Pilih Type Voucher</option>
                                        <option value="fixed" {{ old('type_voucher') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                        <option value="percent" {{ old('type_voucher') == 'percent' ? 'selected' : '' }}>Percent</option>
                                    </select>
                                    <div class="invalid-feedback">Type Voucher harus diisi!</div>
                                </div>

                                <!-- value -->
                                <div class="col-md-6">
                                    <label for="edit_value_display" class="form-label fw-semibold">Value</label>
                                    <input type="text" id="edit_value_display" class="form-control"
                                        placeholder="Masukkan value" required>
                                    <input type="hidden" name="value" id="edit_value">
                                    <div class="invalid-feedback">Value harus diisi!</div>
                                </div>

                                {{-- Quota --}}
                                <div class="col-md-6">
                                    <label for="edit_quota" class="form-label fw-semibold">Quota</label>
                                    <input type="number" name="quota" id="edit_quota" class="form-control"
                                        placeholder="Masukkan quota" required>
                                    <div class="invalid-feedback">Quota harus diisi!</div>
                                </div>

                                {{-- Start Date --}}
                                <div class="col-md-6">
                                    <label for="edit_start_date" class="form-label fw-semibold">Start Date</label>
                                    <input type="date" name="start_date" id="edit_start_date" class="form-control"
                                        placeholder="Masukkan start date" required>
                                    <div class="invalid-feedback">Start date harus diisi!</div>
                                </div>

                                {{-- End Date --}}
                                <div class="col-md-6">
                                    <label for="edit_end_date" class="form-label fw-semibold">End Date</label>
                                    <input type="date" name="end_date" id="edit_end_date" class="form-control"
                                        placeholder="Masukkan end date" required>
                                    <div class="invalid-feedback">End date harus diisi!</div>
                                </div>

                                {{-- Min Purchase --}}
                                <div class="col-md-6">
                                    <label for="edit_min_purchase_display" class="form-label fw-semibold">Min Purchase</label>
                                    <input type="text" id="edit_min_purchase_display" class="form-control"
                                        placeholder="Masukkan min purchase" required>
                                    <input type="hidden" name="min_purchase" id="edit_min_purchase">
                                    <div class="invalid-feedback">Min purchase harus diisi!</div>
                                </div>

                                {{-- Max Discount --}}
                                <div class="col-md-6">
                                    <label for="edit_max_discount_display" class="form-label fw-semibold">Max Discount</label>
                                    <input type="text" id="edit_max_discount_display" class="form-control"
                                        placeholder="Masukkan max discount" required>
                                    <input type="hidden" name="max_discount" id="edit_max_discount">
                                    <div class="invalid-feedback">Max discount harus diisi!</div>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <label for="edit_is_active" class="form-label fw-semibold">Status</label>
                                    <select name="is_active" id="edit_is_active" class="form-control" required>
                                        <option value="">Pilih Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <div class="invalid-feedback">Status harus diisi!</div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="formEditVoucher" class="btn btn-warning text-white px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Delete --}}
    <div id="confirmDeleteModal" class="closecashier-modal">
        <div class="closecashier-modal-content">
            <h2>Hapus Data Voucher</h2>
            <div class="closecashier-body">
                <p id="deleteVoucherInfo">Apakah Anda yakin ingin menghapus Data ini?</p>
            </div>
            <div class="closecashier-footer">
                <button id="btnCancelDelete" class="btn-danger">Batal</button>
                <form id="deleteVoucherForm" method="POST" style="display:inline;">
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
                    Data Voucher Berhasil Dihapus!
                @elseif (session('action') === 'edit')
                    Data Voucher Berhasil Diedit!
                @else
                    Data Voucher Berhasil Ditambahkan!
                @endif
            </h3>
            <p class="success-message">
                @if (session('action') === 'delete')
                    Data Voucher telah dihapus dari sistem.
                @elseif (session('action') === 'edit')
                    Data Voucher telah berhasil diperbarui.   
                @else
                    Data Voucher baru telah berhasil disimpan.
                @endif
            </p>
        </div>
    </div>

    <script>
        function formatNumberToRupiah(value, withPrefix = true) {
            if (value === null || value === undefined) return '';
            let number_string = value.toString().replace(/\D/g, '');
            if (!number_string) return '';
            let sisa = number_string.length % 3;
            let rupiah = number_string.substr(0, sisa);
            let ribuan = number_string.substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return withPrefix ? 'Rp. ' + rupiah : rupiah;
        }

        function parseCurrencyToNumber(value) {
            if (value === null || value === undefined) return '';
            return value.toString().replace(/\D/g, '');
        }

        function setupCurrencyField(displayId, hiddenId, typeSelectId = null) {
            const displayEl = document.getElementById(displayId);
            const hiddenEl = document.getElementById(hiddenId);
            if (!displayEl || !hiddenEl) return;

            function reformat() {
                let withPrefix = true;
                if (typeSelectId) {
                    const typeEl = document.getElementById(typeSelectId);
                    if (typeEl && typeEl.value === 'percent') {
                        withPrefix = false;
                    }
                }
                hiddenEl.value = parseCurrencyToNumber(displayEl.value);
                displayEl.value = formatNumberToRupiah(displayEl.value, withPrefix);
            }
            
            displayEl.addEventListener('input', function(e) {
                let withPrefix = true;
                if (typeSelectId) {
                    const typeEl = document.getElementById(typeSelectId);
                    if (typeEl && typeEl.value === 'percent') {
                        withPrefix = false;
                    }
                }
                
                let originalLen = this.value.length;
                let caretPos = this.selectionStart;

                let rawVal = parseCurrencyToNumber(this.value);
                hiddenEl.value = rawVal;
                this.value = formatNumberToRupiah(this.value, withPrefix);

                let newLen = this.value.length;
                caretPos = caretPos + (newLen - originalLen);
                try { this.setSelectionRange(caretPos, caretPos); } catch (e) {}
            });

            if (typeSelectId) {
                const typeEl = document.getElementById(typeSelectId);
                if (typeEl) {
                    typeEl.addEventListener('change', function() {
                        reformat();
                    });
                }
            }
            
            reformat();
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupCurrencyField('add_value_display', 'add_value', 'add_type_voucher');
            setupCurrencyField('add_min_purchase_display', 'add_min_purchase');
            setupCurrencyField('add_max_discount_display', 'add_max_discount');
            
            setupCurrencyField('edit_value_display', 'edit_value', 'edit_type_voucher');
            setupCurrencyField('edit_min_purchase_display', 'edit_min_purchase');
            setupCurrencyField('edit_max_discount_display', 'edit_max_discount');
        });

        // --- Fungsi Utama untuk Membuka Modal Edit Voucher ---
        async function openEditModal(id) {
            try {
                const response = await fetch(`/get-voucher/${id}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const data = await response.json();
                // console.log(data);
                const form = document.getElementById('formEditVoucher');

                form.action = `/edit-voucher/${id}`;

                document.getElementById('edit_voucher_id').value = data.id ?? '';
                document.getElementById('edit_name').value = data.name ?? '';
                document.getElementById('edit_requirements').value = data.requirements ?? '';
                document.getElementById('edit_type_voucher').value = data.type_voucher ?? '';
                document.getElementById('edit_quota').value = data.quota ?? '';
                document.getElementById('edit_start_date').value = data.start_date ?? '';
                document.getElementById('edit_end_date').value = data.end_date ?? '';
                document.getElementById('edit_is_active').value = data.is_active ?? '';

                document.getElementById('edit_value_display').value = data.value ?? '';
                document.getElementById('edit_value').value = data.value ?? '';
                document.getElementById('edit_min_purchase_display').value = data.min_purchase ?? '';
                document.getElementById('edit_min_purchase').value = data.min_purchase ?? '';
                document.getElementById('edit_max_discount_display').value = data.max_discount ?? '';
                document.getElementById('edit_max_discount').value = data.max_discount ?? '';

                // Trigger format dynamically
                try {
                    document.getElementById('edit_value_display').dispatchEvent(new Event('input'));
                    document.getElementById('edit_min_purchase_display').dispatchEvent(new Event('input'));
                    document.getElementById('edit_max_discount_display').dispatchEvent(new Event('input'));
                } catch(e) {}


                const editModal = new bootstrap.Modal(document.getElementById('modalEditVoucher'));
                editModal.show();
            } catch (error) {
                console.error('Error saat mengambil data Voucher:', error);
                alert('Terjadi kesalahan saat memuat data Voucher. Cek konsol untuk detail.');
            }
        }
        // Logic untuk modal delete (tetap seperti sebelumnya)
        const confirmModal = document.getElementById('confirmDeleteModal');
        const deleteVoucherForm = document.getElementById('deleteVoucherForm');
        const deleteVoucherInfo = document.getElementById('deleteVoucherInfo');
        const cancelBtn = document.getElementById('btnCancelDelete');

        function openConfirmModal(id, name) {
            confirmModal.style.display = 'flex';
            deleteVoucherInfo.innerHTML =    
                `<p>Apakah Anda yakin ingin menghapus Data ini <strong>${name}</strong>?</p>`;
            deleteVoucherForm.action = `/delete-voucher/${id}`;
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

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    showAlert('error', @json($error));
                @endforeach
            @endif

            @if (session('success'))
                showAlert('success', @json(session('success')));
            @endif
        });
    </script>
@endsection
