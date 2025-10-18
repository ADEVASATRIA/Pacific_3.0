@extends('main.back-office')
@section('title', 'Data Promo')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="promo-page">
        <h2 class="page-title">Data Promo</h2>

        {{-- Filter --}}
        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('promo') }}" class="filter-form flex items-end gap-4 flex-wrap">
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
                            data-bs-toggle="modal" data-bs-target="#modalTambahPromo">
                            Tambah Promo
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
                        <th>Code</th>
                        <th>Value</th>
                        <th>Quota</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($promo as $item)
                        <tr>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->value }}</td>
                            <td>{{ $item->quota }}</td>
                            <td>{{ $item->start_date }}</td>
                            <td>{{ $item->expired_date }}</td>
                            <td>
                                @if ($item->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="openEditModal({{ $item->id }})">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="openConfirmModal({{ $item->id }}, '{{ $item->code }}')">
                                    Delete
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 py-3">Tidak ada data promo</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($promo->hasPages())
                <div class="mt-4 flex justify-center">
                    {{ $promo->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    {{-- ✅ Large Modal Tambah Promo --}}
    <div class="modal fade" id="modalTambahPromo" tabindex="-1" aria-labelledby="modalTambahPromoLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold" id="modalTambahPromoLabel">
                        <i class="fas fa-tags me-2"></i> Tambah Promo Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    <form action="{{ route('add.promo') }}" method="POST" id="formTambahPromo" class="needs-validation"
                        novalidate>
                        @csrf

                        <div class="container-fluid">
                            <div class="row g-4">

                                {{-- Kode Promo --}}
                                <div class="col-md-6">
                                    <label for="code" class="form-label fw-semibold">Kode Promo <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="code" name="code" class="form-control shadow-sm"
                                        required>
                                </div>

                                {{-- Deskripsi --}}
                                <div class="col-md-6">
                                    <label for="description" class="form-label fw-semibold">Deskripsi</label>
                                    <textarea id="description" name="description" class="form-control shadow-sm" rows="1"
                                        placeholder="Masukkan deskripsi promo..."></textarea>
                                </div>

                                {{-- Type --}}
                                <div class="col-md-6">
                                    <label for="type" class="form-label fw-semibold">Tipe Promo <span
                                            class="text-danger">*</span></label>
                                    <select id="type" name="type" class="form-select shadow-sm" required>
                                        <option value="1">Persentase</option>
                                        <option value="2">Nominal Tetap</option>
                                    </select>
                                    <small class="text-muted fst-italic">Persentase: nilai dihitung dari total transaksi.
                                        Tetap: nilai tetap.</small>
                                </div>

                                {{-- Nilai --}}
                                <div class="col-md-6">
                                    <label for="value" class="form-label fw-semibold">Nilai <span
                                            class="text-danger">*</span></label>
                                    <input type="number" id="value" name="value" class="form-control shadow-sm"
                                        placeholder="Contoh: 10 atau 50000" required>
                                    <small class="text-muted fst-italic">Jika tipe persentase, jangan tambahkan tanda
                                        %.</small>
                                </div>

                                {{-- Kuota --}}
                                <div class="col-md-6">
                                    <label for="quota" class="form-label fw-semibold">Kuota</label>
                                    <input type="number" id="quota" name="quota" class="form-control shadow-sm"
                                        placeholder="Jumlah maksimal penggunaan">
                                </div>

                                {{-- Start Date --}}
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label fw-semibold">Tanggal Mulai</label>
                                    <input type="date" id="start_date" name="start_date"
                                        class="form-control shadow-sm">
                                </div>

                                {{-- Expired Date --}}
                                <div class="col-md-6">
                                    <label for="expired_date" class="form-label fw-semibold">Tanggal Berakhir</label>
                                    <input type="date" id="expired_date" name="expired_date"
                                        class="form-control shadow-sm">
                                </div>

                                {{-- Min Purchase --}}
                                <div class="col-md-6">
                                    <label for="min_purchase" class="form-label fw-semibold">Minimal Pembelian</label>
                                    <input type="number" id="min_purchase" name="min_purchase"
                                        class="form-control shadow-sm" placeholder="Contoh: 100000">
                                </div>

                                {{-- Max Discount --}}
                                <div class="col-md-6">
                                    <label for="max_discount" class="form-label fw-semibold">Maksimal Diskon</label>
                                    <input type="number" id="max_discount" name="max_discount"
                                        class="form-control shadow-sm" placeholder="Contoh: 50000">
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <label for="is_active" class="form-label fw-semibold">Status <span
                                            class="text-danger">*</span></label>
                                    <select id="is_active" name="is_active" class="form-select shadow-sm" required>
                                        <option value="1">Aktif</option>
                                        <option value="0">Tidak Aktif</option>
                                    </select>
                                </div>

                                {{-- Select Tickets --}}
                                <div class="col-12">
                                    <label for="" class="form-label fw-semibold d-block mb-2">Pilih Tiket yang
                                        Berlaku</label>
                                    <div class="border bg-white p-3 rounded-3 shadow-sm">
                                        <div class="row">
                                            @foreach ($tickets as $ticket)
                                                <div class="col-md-4 col-sm-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            value="{{ $ticket->id }}" id="ticket_{{ $ticket->id }}"
                                                            name="ticket_types[]"
                                                            {{ old('ticket_types') ? (in_array($ticket->id, old('ticket_types')) ? 'checked' : '') : 'checked' }}>
                                                        <label class="form-check-label" for="ticket_{{ $ticket->id }}">
                                                            {{ $ticket->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
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

    {{-- ✅ Modal Edit Promo --}}
    <div class="modal fade" id="modalEditPromo" tabindex="-1" aria-labelledby="modalEditPromoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                <div class="modal-header bg-warning text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold" id="modalEditPromoLabel">
                        <i class="fas fa-edit me-2"></i> Edit Data Promo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body bg-light py-4">
                    <form id="formEditPromo" method="POST" class="needs-validation" novalidate>
                        @csrf
                        {{-- nanti action diisi via JS: /edit-promo/{id} --}}
                        <div class="container-fluid">
                            <div class="row g-4">
                                {{-- semua field sama seperti tambah promo --}}
                                <div class="col-md-6">
                                    <label for="edit_code" class="form-label fw-semibold">Kode Promo</label>
                                    <input type="text" id="edit_code" name="code" class="form-control shadow-sm"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_description" class="form-label fw-semibold">Deskripsi</label>
                                    <textarea id="edit_description" name="description" class="form-control shadow-sm" rows="1"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_type" class="form-label fw-semibold">Tipe Promo</label>
                                    <select id="edit_type" name="type" class="form-select shadow-sm" required>
                                        <option value="1">Persentase</option>
                                        <option value="2">Nominal Tetap</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_value" class="form-label fw-semibold">Nilai</label>
                                    <input type="number" id="edit_value" name="value" class="form-control shadow-sm"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_quota" class="form-label fw-semibold">Kuota</label>
                                    <input type="number" id="edit_quota" name="quota" class="form-control shadow-sm">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_start_date" class="form-label fw-semibold">Tanggal Mulai</label>
                                    <input type="date" id="edit_start_date" name="start_date"
                                        class="form-control shadow-sm">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_expired_date" class="form-label fw-semibold">Tanggal Berakhir</label>
                                    <input type="date" id="edit_expired_date" name="expired_date"
                                        class="form-control shadow-sm">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_min_purchase" class="form-label fw-semibold">Minimal
                                        Pembelian</label>
                                    <input type="number" id="edit_min_purchase" name="min_purchase"
                                        class="form-control shadow-sm">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_max_discount" class="form-label fw-semibold">Maksimal Diskon</label>
                                    <input type="number" id="edit_max_discount" name="max_discount"
                                        class="form-control shadow-sm">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_is_active" class="form-label fw-semibold">Status</label>
                                    <select id="edit_is_active" name="is_active" class="form-select shadow-sm" required>
                                        <option value="1">Aktif</option>
                                        <option value="0">Tidak Aktif</option>
                                    </select>
                                </div>

                                {{-- Select Tickets --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold d-block mb-2">Pilih Tiket yang Berlaku</label>
                                    <div class="border bg-white p-3 rounded-3 shadow-sm">
                                        <div class="row">
                                            @foreach ($tickets as $ticket)
                                                <div class="col-md-4 col-sm-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input edit-ticket-checkbox"
                                                            type="checkbox" value="{{ $ticket->id }}"
                                                            id="edit_ticket_{{ $ticket->id }}" name="ticket_types[]">
                                                        <label class="form-check-label"
                                                            for="edit_ticket_{{ $ticket->id }}">
                                                            {{ $ticket->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" form="formEditPromo" class="btn btn-warning text-white px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>



    {{-- ====================== --}}
    {{-- ✅ Modal Konfirmasi Delete --}}
    {{-- ====================== --}}
    <div id="confirmDeleteModal" class="closecashier-modal">
        <div class="closecashier-modal-content">
            <h2>Hapus Promo</h2>
            <div class="closecashier-body">
                <p id="deletePromoInfo">Apakah Anda yakin ingin menghapus promo ini?</p>
            </div>
            <div class="closecashier-footer">
                <button id="btnCancelDelete" class="btn-danger">Batal</button>
                <form id="deletePromoForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-success">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ====================== --}}
    {{-- ✅ Modal Success --}}
    {{-- ====================== --}}
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
                    Promo Berhasil Dihapus!
                @elseif (session('action') === 'edit')
                    Promo Berhasil Diedit!
                @else
                    Promo Berhasil Ditambahkan!
                @endif
            </h3>
            <p class="success-message">
                @if (session('action') === 'delete')
                    Data promo telah dihapus dari sistem.
                @elseif (session('action') === 'edit')
                    Data promo telah berhasil diperbarui.
                @else
                    Data promo baru telah berhasil disimpan.
                @endif
            </p>

        </div>
    </div>

    {{-- Script Modal Logic --}}
    <script>
        // Interaksi dinamis antara type dan value
        const typeSelect = document.querySelector('#type');
        const valueInput = document.querySelector('#value');

        typeSelect.addEventListener('change', () => {
            if (typeSelect.value === '2') {
                valueInput.type = 'text';
                valueInput.placeholder = 'Masukkan nilai tetap (contoh: 50000)';
            } else {
                valueInput.type = 'number';
                valueInput.placeholder = 'Masukkan nilai persentase (contoh: 10)';
            }
            valueInput.value = '';
        });

        const confirmModal = document.getElementById('confirmDeleteModal');
        const successModal = document.getElementById('successModal');
        const deletePromoForm = document.getElementById('deletePromoForm');
        const promoInfo = document.getElementById('deletePromoInfo');
        const cancelBtn = document.getElementById('btnCancelDelete');

        function openConfirmModal(id, code) {
            confirmModal.style.display = 'flex';
            promoInfo.innerHTML = `<p>Apakah Anda yakin ingin menghapus promo <strong>${code}</strong>?</p>`;
            deletePromoForm.action = `/delete-promo/${id}`;
        }

        async function openEditModal(id) {
            try {
                const response = await fetch(`/get-promo/${id}`);
                const data = await response.json();
                // console.log(data);

                // ambil ticket_types langsung dari data (bukan result.ticket_types)
                const ticketTypes = Array.isArray(data.ticket_types) ?
                    data.ticket_types :
                    [];

                // isi field form edit
                document.getElementById('formEditPromo').action = `/edit-promo/${id}`;
                document.getElementById('edit_code').value = data.code ?? '';
                document.getElementById('edit_description').value = data.description ?? '';
                document.getElementById('edit_type').value = data.type ?? '';
                document.getElementById('edit_value').value = data.value ?? '';
                document.getElementById('edit_quota').value = data.quota ?? '';
                document.getElementById('edit_start_date').value = data.start_date ?? '';
                document.getElementById('edit_expired_date').value = data.expired_date ?? '';
                document.getElementById('edit_min_purchase').value = data.min_purchase ?? '';
                document.getElementById('edit_max_discount').value = data.max_discount ?? '';
                document.getElementById('edit_is_active').value = data.is_active ?? 1;

                // reset semua checkbox tiket
                document.querySelectorAll('.edit-ticket-checkbox').forEach(cb => cb.checked = false);

                // centang tiket sesuai data ticket_types
                ticketTypes.forEach(ticketId => {
                    const checkbox = document.getElementById(`edit_ticket_${ticketId}`);
                    if (checkbox) checkbox.checked = true;
                });

                // tampilkan modal edit
                const modal = new bootstrap.Modal(document.getElementById('modalEditPromo'));
                modal.show();
            } catch (error) {
                alert('Gagal memuat data promo.');
                console.error(error);
            }
        }



        cancelBtn.addEventListener('click', () => {
            confirmModal.style.display = 'none';
        });

        // ✅ Munculkan modal success jika session success true
        @if (session('success'))
            window.addEventListener('load', () => {
                successModal.style.display = 'flex';
                setTimeout(() => {
                    successModal.style.display = 'none';
                }, 2500);
            });
        @endif
    </script>
@endsection
