@extends('main.back_blank')
@section('title', 'Data Ticket Types')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="ticket-types-page">
        <h2 class="page-title">Data Tickets Types</h2>

        {{-- Filter --}}
        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('ticket-types') }}" class="filter-form flex items-end gap-4 flex-wrap">
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
                            data-bs-toggle="modal" data-bs-target="#modalTambahTicketTypes">
                            Tambah Ticket Type
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table Section --}}
        <div class="table-section mt-2 relative"> {{-- tambahkan relative agar gradient scroll bisa muncul --}}
            <div class="table-wrapper"> {{-- tambahan pembungkus untuk memastikan scroll tidak terpotong --}}
                <div class="table-scroll-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Durasi</th>
                                <th>Extra Ticket</th>
                                <th>Urutan</th>
                                <th>Butuh DOB</th>
                                <th>Butuh Telephone</th>
                                <th>Aktif</th>
                                <th>Bisa Beli <br>Tiket Pengantar</th>
                                <th>Bisa Input Coach <br>/ Clubhouse</th>
                                <th>Tipe Khusus</th>
                                <th>Tiket Kode REF</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ticketTypes as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $item->duration }}</td>
                                    <td class="text-center">{{ $item->qty_extra }}</td>
                                    <td class="text-center">{{ $item->weight }}</td>
                                    <td class="text-center">{!! $item->getBadgeHtml($item->is_dob_mandatory) !!}</td>
                                    <td class="text-center">{!! $item->getBadgeHtml($item->is_phone_mandatory) !!}</td>
                                    <td class="text-center">{!! $item->getBadgeHtml($item->is_active) !!}</td>
                                    <td class="text-center">{!! $item->getBadgeHtml($item->can_buy_tiket_pengantar) !!}</td>
                                    <td class="text-center">{!! $item->getBadgeHtml($item->is_coach_club_require) !!}</td>
                                    <td class="text-center">
                                        @switch($item->tipe_khusus)
                                            @case(1)
                                                <span class="badge bg-info text-dark">Normal</span>
                                            @break

                                            @case(2)
                                                <span class="badge bg-warning">Pengantar</span>
                                            @break

                                            @case(3)
                                                <span class="badge bg-primary">Pelatih</span>
                                            @break

                                            @case(4)
                                                <span class="badge bg-dark">Member</span>
                                            @break

                                            @case(5)
                                                <span class="badge bg-warning text-dark">Biaya Pelatih</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td class="text-center">{{ $item->ticket_kode_ref }}</td>
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
                                        <td colspan="13" class="text-center text-gray-500 py-3">
                                            Tidak ada data tiket
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                @if ($ticketTypes->hasPages())
                    <div class="mt-4 flex justify-center">
                        {{ $ticketTypes->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            {{-- Modal Tambah data Ticket Types --}}
            <div class="modal fade" id="modalTambahTicketTypes" tabindex="-1" aria-labelledby="modalTambahTicketTypesLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                        {{-- Header --}}
                        <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                            <h5 class="modal-title fw-semibold text-black" id="modalTambahTicketTypesLabel">
                                <i class="fas fa-tags me-2"></i>Tambah Ticket Type Baru
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        {{-- Body --}}
                        <div class="modal-body bg-light py-4">
                            <form action="{{ route('add.ticket_types') }}" method="POST" id="formTambahTicketTypes"
                                class="needs-validation" novalidate>
                                @csrf

                                <div class="container-fluid">
                                    <div class="row g-4">
                                        {{-- Nama Tiket --}}
                                        <div class="col-md-6">
                                            <label for="name" class="form-label fw-semibold">Nama Tiket</label><span
                                                class="text-danger">*</span>
                                            <input type="text" id="name" name="name" class="form-control shadow-sm"
                                                required>
                                        </div>

                                        {{-- Harga Tiket --}}
                                        <div class="col-md-6">
                                            <label for="price" class="form-label fw-semibold">Harga Tiket</label><span
                                                class="text-danger">*</span>
                                            <input type="number" id="price" name="price" class="form-control shadow-sm"
                                                required>
                                        </div>

                                        {{-- Durasi Tiket --}}
                                        <div class="col-md-6">
                                            <label for="duration" class="form-label fw-semibold">Durasi (hari)</label><span
                                                class="text-danger">*</span>
                                            <input type="number" id="duration" name="duration" class="form-control shadow-sm"
                                                required>
                                        </div>

                                        {{-- Extra Ticket --}}
                                        <div class="col-md-6">
                                            <label for="qty_extra" class="form-label fw-semibold">Extra Tiket</label>
                                            <input type="number" id="qty_extra" name="qty_extra"
                                                class="form-control shadow-sm">
                                        </div>

                                        {{-- Weight --}}
                                        <div class="col-md-6">
                                            <label for="weight" class="form-label fw-semibold">Urutan Prioritas</label>
                                            <input type="number" id="weight" name="weight"
                                                class="form-control shadow-sm" min="1">
                                        </div>

                                        {{-- Ticket Kode Ref --}}
                                        <div class="col-md-6">
                                            <label for="ticket_kode_ref" class="form-label fw-semibold">Ticket Code
                                                Reference</label><span class="text-danger">*</span>
                                            <input type="text" id="ticket_kode_ref" name="ticket_kode_ref"
                                                class="form-control shadow-sm" required>
                                        </div>

                                        {{-- DOB Mandatory --}}
                                        <div class="col-md-6">
                                            <label for="is_dob_mandatory" class="form-label fw-semibold">DOB Required</label>
                                            <select name="is_dob_mandatory" id="is_dob_mandatory"
                                                class="form-select shadow-sm" required>
                                                <option value="1">Iya</option>
                                                <option value="0">Tidak</option>
                                            </select>
                                        </div>

                                        {{-- Phone Mandatory --}}
                                        <div class="col-md-6">
                                            <label for="is_phone_mandatory" class="form-label fw-semibold">Phone
                                                Required</label>
                                            <select name="is_phone_mandatory" id="is_phone_mandatory"
                                                class="form-select shadow-sm" required>
                                                <option value="1">Iya</option>
                                                <option value="0">Tidak</option>
                                            </select>
                                        </div>

                                        {{-- Active --}}
                                        <div class="col-md-6">
                                            <label for="is_active" class="form-label fw-semibold">Status Aktif</label>
                                            <select name="is_active" id="is_active" class="form-select shadow-sm" required>
                                                <option value="1">Iya</option>
                                                <option value="0">Tidak</option>
                                            </select>
                                        </div>

                                        {{-- Bisa Beli Tiket Pengantar --}}
                                        <div class="col-md-6">
                                            <label for="can_buy_tiket_pengantar" class="form-label fw-semibold">Bisa Beli
                                                Tiket
                                                Pengantar</label>
                                            <select name="can_buy_tiket_pengantar" id="can_buy_tiket_pengantar"
                                                class="form-select shadow-sm" required>
                                                <option value="1">Iya</option>
                                                <option value="0">Tidak</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="is_coach_club_require" class="form-label fw-semibold">Bisa Bisa input
                                                coach /
                                                clubhouse</label>
                                            <select name="is_coach_club_require" id="is_coach_club_require"
                                                class="form-select shadow-sm" required>
                                                <option value="1">Iya</option>
                                                <option value="0">Tidak</option>
                                            </select>
                                        </div>

                                        {{-- Tipe Khusus --}}
                                        <div class="col-md-6" id="tipeKhusus">
                                            <label for="tipe_khusus" class="form-label fw-semibold">Tipe Khusus</label>
                                            <select name="tipe_khusus" id="tipe_khusus" class="form-select shadow-sm"
                                                required>
                                                <option value="1">Normal</option>
                                                <option value="2">Pengantar</option>
                                                <option value="3">Pelatih</option>
                                                <option value="4">Member</option>
                                                <option value="5">Biaya Pelatih</option>
                                            </select>
                                        </div>

                                        {{-- Submit --}}
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary shadow-sm">
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

            {{-- Modal edit data ticket Types --}}
            <div class="modal fade" id="modalEditTicketTypes" tabindex="-1" aria-labelledby="modalEditTicketTypesLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                        {{-- Header --}}
                        <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                            <h5 class="modal-title fw-semibold" id="modalEditTicketTypesLabel">
                                <i class="fas fa-tags me-2"></i>Tambah Ticket Type Baru
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        {{-- Body --}}
                        <div class="modal-body bg-light py-4">
                            <form id="formEditTicketTypes" method="POST" class="needs-validation" novalidate>
                                @csrf

                                <div class="container-fluid">
                                    <div class="row g-4">
                                        {{-- Nama Tiket --}}
                                        <div class="col-md-6">
                                            <label for="edit_name" class="form-label fw-semibold">Nama Tiket</label><span
                                                class="text-danger">*</span>
                                            <input type="text" id="edit_name" name="name"
                                                class="form-control shadow-sm" required>
                                        </div>

                                        {{-- Harga Tiket --}}
                                        <div class="col-md-6">
                                            <label for="edit_price" class="form-label fw-semibold">Harga Tiket</label><span
                                                class="text-danger">*</span>
                                            <input type="number" id="edit_price" name="price"
                                                class="form-control shadow-sm" required>
                                        </div>

                                        {{-- Durasi Tiket --}}
                                        <div class="col-md-6">
                                            <label for="edit_duration" class="form-label fw-semibold">Durasi
                                                (hari)</label><span class="text-danger">*</span>
                                            <input type="number" id="edit_duration" name="duration"
                                                class="form-control shadow-sm" required>
                                        </div>

                                        {{-- Extra Ticket --}}
                                        <div class="col-md-6">
                                            <label for="edit_qty_extra" class="form-label fw-semibold">Extra Tiket</label>
                                            <input type="number" id="edit_qty_extra" name="qty_extra"
                                                class="form-control shadow-sm">
                                        </div>

                                        {{-- Weight --}}
                                        <div class="col-md-6">
                                            <label for="edit_weight" class="form-label fw-semibold">Urutan Prioritas</label>
                                            <input type="number" id="edit_weight" name="weight"
                                                class="form-control shadow-sm" min="1">
                                        </div>

                                        {{-- Ticket Kode Ref --}}
                                        <div class="col-md-6">
                                            <label for="edit_ticket_kode_ref" class="form-label fw-semibold">Ticket Code
                                                Reference</label><span class="text-danger">*</span>
                                            <input type="text" id="edit_ticket_kode_ref" name="ticket_kode_ref"
                                                class="form-control shadow-sm" required>
                                        </div>

                                        {{-- DOB Mandatory --}}
                                        <div class="col-md-6">
                                            <label for="edit_is_dob_mandatory" class="form-label fw-semibold">DOB
                                                Required</label>
                                            <select name="is_dob_mandatory" id="edit_is_dob_mandatory"
                                                class="form-select shadow-sm" required>
                                                <option value="1">Iya</option>
                                                <option value="0">Tidak</option>
                                            </select>
                                        </div>

                                        {{-- Phone Mandatory --}}
                                        <div class="col-md-6">
                                            <label for="edit_is_phone_mandatory" class="form-label fw-semibold">Phone
                                                Required</label>
                                            <select name="is_phone_mandatory" id="edit_is_phone_mandatory"
                                                class="form-select shadow-sm" required>
                                                <option value="1">Iya</option>
                                                <option value="0">Tidak</option>
                                            </select>
                                        </div>

                                        {{-- Active --}}
                                        <div class="col-md-6">
                                            <label for="edit_is_active" class="form-label fw-semibold">Status Aktif</label>
                                            <select name="is_active" id="edit_is_active" class="form-select shadow-sm"
                                                required>
                                                <option value="1">Iya</option>
                                                <option value="0">Tidak</option>
                                            </select>
                                        </div>

                                        {{-- Bisa Beli Tiket Pengantar --}}
                                        <div class="col-md-6">
                                            <label for="edit_can_buy_tiket_pengantar" class="form-label fw-semibold">Bisa Beli
                                                Tiket
                                                Pengantar</label>
                                            <select name="can_buy_tiket_pengantar" id="edit_can_buy_tiket_pengantar"
                                                class="form-select shadow-sm" required>
                                                <option value="1">Iya</option>
                                                <option value="0">Tidak</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="edit_is_coach_club_require" class="form-label fw-semibold">Bisa Bisa
                                                input coach /
                                                clubhouse</label>
                                            <select name="is_coach_club_require" id="edit_is_coach_club_require"
                                                class="form-select shadow-sm" required>
                                                <option value="1">Iya</option>
                                                <option value="0">Tidak</option>
                                            </select>
                                        </div>

                                        {{-- Tipe Khusus --}}
                                        <div class="col-md-6" id="edit_tipeKhusus">
                                            <label for="edit_tipeKhusus" class="form-label fw-semibold">Tipe Khusus</label>
                                            <select name="tipe_khusus" id="edit_tipeKhusus" class="form-select shadow-sm"
                                                required>
                                                <option value="1">Normal</option>
                                                <option value="2">Pengantar</option>
                                                <option value="3">Pelatih</option>
                                                <option value="4">Member</option>
                                                <option value="5">Biaya Pelatih</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" form="formEditTicketTypes" class="btn btn-warning text-white px-4">
                                <i class="fas fa-save me-1"></i> Update
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Modal Delete --}}
            <div id="confirmDeleteModal" class="closecashier-modal">
                <div class="closecashier-modal-content">
                    <h2>Hapus Ticket Types</h2>
                    <div class="closecashier-body">
                        <p id="deleteTicketTypeInfo">Apakah Anda yakin ingin menghapus Data ini?</p>
                    </div>
                    <div class="closecashier-footer">
                        <button id="btnCancelDelete" class="btn-danger">Batal</button>
                        <form id="deleteTicketTypeForm" method="POST" style="display:inline;">
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
                            Ticket Types Berhasil Dihapus!
                        @elseif (session('action') === 'edit')
                            Ticket Types Berhasil Diedit!
                        @else
                            Ticket Types Berhasil Ditambahkan!
                        @endif
                    </h3>
                    <p class="success-message">
                        @if (session('action') === 'delete')
                            Data Ticket Types telah dihapus dari sistem.
                        @elseif (session('action') === 'edit')
                            Data Ticket Types telah berhasil diperbarui.
                        @else
                            Data Ticket Types baru telah berhasil disimpan.
                        @endif
                    </p>

                </div>
            </div>


            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const canBuyTiket = document.getElementById('can_buy_tiket_pengantar');
                    const tipeKhusus = document.getElementById('tipeKhusus');

                    function toggleTipeKhusus() {
                        tipeKhusus.style.display = (canBuyTiket.value === '1') ? 'none' : 'block';
                    }

                    toggleTipeKhusus();
                    canBuyTiket.addEventListener('change', toggleTipeKhusus);
                });

                const confirmModal = document.getElementById('confirmDeleteModal');
                const successModal = document.getElementById('successModal');
                const deleteTicketTypeForm = document.getElementById('deleteTicketTypeForm');
                const ticketTypeInfo = document.getElementById('deleteTicketTypeInfo');
                const cancelBtn = document.getElementById('btnCancelDelete');

                function openConfirmModal(id, name) {
                    confirmModal.style.display = 'flex';
                    ticketTypeInfo.innerHTML = `<p>Apakah Anda yakin ingin menghapus promo <strong>${name}</strong>?</p>`;
                    deleteTicketTypeForm.action = `/delete-ticket-type/${id}`;
                }

                async function openEditModal(id) {
                    try {
                        // Ambil data ticket type dari backend
                        const response = await fetch(`/get-ticket-types/${id}`);
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                        const data = await response.json();
                        console.log('Ticket Type Data:', data);

                        // Pastikan form action diarahkan ke route edit
                        document.getElementById('formEditTicketTypes').action = `/edit-ticket-type/${id}`;

                        // Isi semua field form edit sesuai data dari response
                        document.getElementById('edit_name').value = data.name ?? '';
                        document.getElementById('edit_price').value = data.price ?? '';
                        document.getElementById('edit_duration').value = data.duration ?? '';
                        document.getElementById('edit_qty_extra').value = data.qty_extra ?? '';
                        document.getElementById('edit_weight').value = data.weight ?? '';
                        document.getElementById('edit_ticket_kode_ref').value = data.ticket_kode_ref ?? '';

                        document.getElementById('edit_is_dob_mandatory').value = data.is_dob_mandatory ?? 0;
                        document.getElementById('edit_is_phone_mandatory').value = data.is_phone_mandatory ?? 0;
                        document.getElementById('edit_is_active').value = data.is_active ?? 1;
                        document.getElementById('edit_can_buy_tiket_pengantar').value = data.can_buy_tiket_pengantar ?? 0;
                        document.getElementById('edit_is_coach_club_require').value = data.is_coach_club_require ?? 0;
                        document.getElementById('edit_tipeKhusus').value = data.tipe_khusus ?? 1;

                        // Tampilkan modal edit
                        const modal = new bootstrap.Modal(document.getElementById('modalEditTicketTypes'));
                        modal.show();

                        // Jalankan toggle visibilitas untuk "Tipe Khusus" (agar konsisten)
                        toggleEditTipeKhusus();

                    } catch (error) {
                        alert('Gagal memuat data Ticket Type.');
                        console.error('Error:', error);
                    }
                }

                // Fungsi untuk sembunyikan field "Tipe Khusus" jika can_buy_tiket_pengantar = 1
                function toggleEditTipeKhusus() {
                    const canBuyTiket = document.getElementById('edit_can_buy_tiket_pengantar');
                    const tipeKhususContainer = document.getElementById('edit_tipeKhusus').closest('div');

                    tipeKhususContainer.style.display = (canBuyTiket.value === '1') ? 'none' : 'block';
                }

                // Event listener agar toggle tetap aktif kalau user ubah dropdown
                document.addEventListener('DOMContentLoaded', function() {
                    const canBuyTiket = document.getElementById('edit_can_buy_tiket_pengantar');
                    if (canBuyTiket) {
                        canBuyTiket.addEventListener('change', toggleEditTipeKhusus);
                    }
                });



                cancelBtn.addEventListener('click', () => {
                    confirmModal.style.display = 'none';
                });
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
