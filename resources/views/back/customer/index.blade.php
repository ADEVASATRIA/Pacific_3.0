@extends('main.back-office')
@section('title', 'Data Customer')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="promo-page">
        <h2 class="page-title">Data Customer</h2>
        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('customer') }}" class="filter-form flex items-end gap-4 flex-wrap">

                <div class="row g-3">
                    <div class="col-md-auto">
                        <label for="name" class="form-label block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="name" id="name"
                            class="form-control mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Cari Nama" value="{{ request('name') }}">
                    </div>

                    <div class="col-md-auto">
                        <label for="phone" class="form-label block text-sm font-medium text-gray-700">No. Telepon</label>
                        <input type="text" name="phone" id="phone"
                            class="form-control mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Cari No. Telepon" value="{{ request('phone') }}">
                    </div>

                </div>

                <div class="form-group">
                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700">
                            Filter
                        </button>
                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700"
                            data-bs-toggle="modal" data-bs-target="#modalTambahCustomer">
                            Tambah Customer
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-section mt-4">
            <table class="table w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center">Nama</th>
                        <th class="text-center">Nomor Telephone</th>
                        <th class="text-center">Tanggal Lahir</th>
                        <th class="text-center">Tipe Customer</th>
                        <th class="text-center">Kategory Customer</th>
                        <th class="text-center">Nama Club Renang</th>
                        <th class="text-center">Catatan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td> {{ $customer->dob ? \Carbon\Carbon::parse($customer->dob)->format('d M Y') : '-' }}
                            </td>
                            <td class="text-center">
                                @php
                                    if ($customer->tipe_customer == '1') {
                                        echo 'Pria';
                                    } elseif ($customer->tipe_customer == '2') {
                                        echo 'Wanita';
                                    } elseif ($customer->tipe_customer == '3') {
                                        echo 'Anak-Anak';
                                    } else {
                                        echo 'Data Masih Belum Ada';
                                    }
                                @endphp
                            </td>

                            <td class="text-center">
                                @php
                                    if ($customer->kategory_customer == '1') {
                                        echo 'Umum';
                                    } elseif ($customer->kategory_customer == '2') {
                                        echo 'Coach';
                                    } elseif ($customer->kategory_customer == '3') {
                                        echo 'Private';
                                    } else {
                                        echo 'Data Masih Belum Ada';
                                    }
                                @endphp
                            </td>
                            <td class="text-center">
                                {{ $customer->clubhouse ? $customer->clubhouse->name : 'Tidak ada data' }}
                            </td>
                            <td>
                                {{ $customer->catatan }}
                            </td>

                            <td class="text-center">
                                <button class="btn btn-primary btn-sm" onclick="openEditModal({{ $customer->id }})">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="openConfirmModal({{ $customer->id }}, '{{ $customer->name }}')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 py-3">
                                Tidak ada data Customer
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($customers->hasPages())
                <div class="mt-4 flex justify-center">
                    {{ $customers->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Add Customer --}}
    <div class="modal fade" id="modalTambahCustomer" tabindex="-1" aria-labelledby="modalTambahCustomerLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold" id="modalTambahCustomerLabel">
                        <i class="fas fa-user-plus me-2"></i> Tambah Customer Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body bg-light py-4">
                    <form action="{{ route('add.customer') }}" method="POST" id="formTambahCustomer"
                        class="needs-validation" novalidate>
                        @csrf

                        <div class="container-fluid">
                            <div class="row g-4">

                                {{-- Nama & Telepon --}}
                                <div class="col-md-6">
                                    <label for="add_name" class="form-label fw-semibold">Nama Customer</label>
                                    <input type="text" name="name" id="add_name" class="form-control"
                                        placeholder="Masukkan nama lengkap" required value="{{ old('name') }}">
                                    <div class="invalid-feedback">Nama wajib diisi!</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="add_phone" class="form-label fw-semibold">Nomor Telepon</label>
                                    <input type="text" name="phone" id="add_phone" class="form-control"
                                        placeholder="Nomor Telepon (maks 15 digit)" required value="{{ old('phone') }}">
                                    <div class="invalid-feedback">Nomor Telepon wajib diisi!</div>
                                </div>

                                {{-- Tanggal Lahir & Clubhouse --}}
                                <div class="col-md-6">
                                    <label for="add_dob" class="form-label fw-semibold">Tanggal Lahir</label>
                                    <input type="date" name="dob" id="add_dob" class="form-control"
                                        value="{{ old('dob') }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="add_id_club_renang" class="form-label fw-semibold">Clubhouse</label>
                                    <select class="form-select" id="add_id_club_renang" name="id_club_renang" required>
                                        <option value="" disabled selected>Pilih Clubhouse</option>
                                        {{-- Asumsikan variabel $clubhouses tersedia --}}
                                        @if (isset($clubhouses))
                                            @foreach ($clubhouses as $cb)
                                                <option value="{{ $cb->id }}"
                                                    {{ old('id_club_renang') == $cb->id ? 'selected' : '' }}>
                                                    {{ $cb->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="invalid-feedback">Clubhouse wajib dipilih!</div>
                                </div>

                                {{-- Tipe Customer & Kategori Customer --}}
                                <div class="col-md-6">
                                    <label for="add_tipe_customer" class="form-label fw-semibold">Tipe Customer</label>
                                    <select class="form-select" id="add_tipe_customer" name="tipe_customer" required>
                                        <option value="" disabled selected>Pilih Tipe</option>
                                        {{-- Ganti label sesuai dengan definisi tipe (1, 2, 3) di sistem Anda --}}
                                        <option value="1" {{ old('tipe_customer') == '1' ? 'selected' : '' }}>Tipe 1:
                                            Pria</option>
                                        <option value="2" {{ old('tipe_customer') == '2' ? 'selected' : '' }}>Tipe 2:
                                            Wanita</option>
                                        <option value="3" {{ old('tipe_customer') == '3' ? 'selected' : '' }}>Tipe 3:
                                            Anak-Anak</option>
                                    </select>
                                    <div class="invalid-feedback">Tipe Customer wajib dipilih!</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="add_kategory_customer" class="form-label fw-semibold">Kategori
                                        Customer</label>
                                    <select class="form-select" id="add_kategory_customer" name="kategory_customer"
                                        required>
                                        <option value="" disabled selected>Pilih Kategori</option>
                                        {{-- Kategori 2 = Pelatih (yang memicu masa berlaku) --}}
                                        <option value="1" {{ old('kategory_customer') == '1' ? 'selected' : '' }}>
                                            Kategori 1: Umum</option>
                                        <option value="2" {{ old('kategory_customer') == '2' ? 'selected' : '' }}>
                                            Kategori 2: Pelatih</option>
                                        <option value="3" {{ old('kategory_customer') == '3' ? 'selected' : '' }}>
                                            Kategori 3: Private</option>
                                    </select>
                                    <div class="invalid-feedback">Kategori Customer wajib dipilih!</div>
                                </div>

                                {{-- Catatan --}}
                                <div class="col-12">
                                    <label for="add_catatan" class="form-label fw-semibold">Catatan</label>
                                    <textarea name="catatan" id="add_catatan" class="form-control" rows="2"
                                        placeholder="Catatan tambahan (maks 255 karakter)">{{ old('catatan') }}</textarea>
                                </div>

                                <hr class="mt-4 mb-0">

                                {{-- Wrapper Masa Berlaku (Hanya muncul jika Kategori = 2) --}}
                                <div class="row g-4" id="masa_berlaku_wrapper" style="display: none;">
                                    <div class="col-12">
                                        <h6 class="fw-bold text-primary">Detail Masa Berlaku (Khusus Pelatih)</h6>
                                    </div>

                                    {{-- Awal Masa Berlaku --}}
                                    <div class="col-md-6">
                                        <label for="add_awal_masa_berlaku" class="form-label fw-semibold">Awal Masa
                                            Berlaku</label>
                                        <input type="date" name="awal_masa_berlaku" id="add_awal_masa_berlaku"
                                            class="form-control" value="{{ old('awal_masa_berlaku') }}">
                                        <div class="invalid-feedback">Awal Masa Berlaku wajib diisi untuk Pelatih!</div>
                                    </div>

                                    {{-- Akhir Masa Berlaku --}}
                                    <div class="col-md-6">
                                        <label for="add_akhir_masa_berlaku" class="form-label fw-semibold">Akhir Masa
                                            Berlaku</label>
                                        <input type="date" name="akhir_masa_berlaku" id="add_akhir_masa_berlaku"
                                            class="form-control" value="{{ old('akhir_masa_berlaku') }}">
                                        <div class="invalid-feedback">Akhir Masa Berlaku wajib diisi untuk Pelatih!</div>
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
                    <button type="submit" form="formTambahCustomer" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal Edit Customer --}}
    <div class="modal fade" id="modalEditCustomer" tabindex="-1" aria-labelledby="modalEditCustomerLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold" id="modalEditCustomerLabel">
                        <i class="fas fa-user-edit me-2"></i> Edit Customer
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body bg-light py-4">
                    <form id="formEditCustomer" method="POST" class="needs-validation" novalidate>
                        @csrf
                        {{-- Input tersembunyi untuk ID Customer --}}
                        <input type="hidden" name="id" id="edit_customer_id">

                        <div class="container-fluid">
                            <div class="row g-4">

                                {{-- Nama & Telepon --}}
                                <div class="col-md-6">
                                    <label for="edit_name" class="form-label fw-semibold">Nama Customer</label>
                                    <input type="text" name="name" id="edit_name" class="form-control"
                                        placeholder="Masukkan nama lengkap" required>
                                    <div class="invalid-feedback">Nama wajib diisi!</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_phone" class="form-label fw-semibold">Nomor Telepon</label>
                                    <input type="text" name="phone" id="edit_phone" class="form-control"
                                        placeholder="Nomor Telepon (maks 15 digit)" required>
                                    <div class="invalid-feedback">Nomor Telepon wajib diisi!</div>
                                </div>

                                {{-- Tanggal Lahir & Clubhouse --}}
                                <div class="col-md-6">
                                    <label for="edit_dob" class="form-label fw-semibold">Tanggal Lahir</label>
                                    <input type="date" name="dob" id="edit_dob" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_id_club_renang" class="form-label fw-semibold">Clubhouse</label>
                                    {{-- Opsi Clubhouse akan diisi oleh JavaScript --}}
                                    <select class="form-select" id="edit_id_club_renang" name="id_club_renang" required>
                                        <option value="" disabled>Memuat...</option>
                                    </select>
                                    <div class="invalid-feedback">Clubhouse wajib dipilih!</div>
                                </div>

                                {{-- Tipe Customer & Kategori Customer --}}
                                <div class="col-md-6">
                                    <label for="edit_tipe_customer" class="form-label fw-semibold">Tipe Customer</label>
                                    {{-- Opsi Tipe akan diisi/diseleksi oleh JavaScript --}}
                                    <select class="form-select" id="edit_tipe_customer" name="tipe_customer" required>
                                        <option value="1">Tipe 1: Pria</option>
                                        <option value="2">Tipe 2: Wanita</option>
                                        <option value="3">Tipe 3: Anak-Anak</option>
                                    </select>
                                    <div class="invalid-feedback">Tipe Customer wajib dipilih!</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_kategory_customer" class="form-label fw-semibold">Kategori
                                        Customer</label>
                                    {{-- Opsi Kategori akan diisi/diseleksi oleh JavaScript --}}
                                    <select class="form-select" id="edit_kategory_customer" name="kategory_customer"
                                        required>
                                        <option value="1">Kategori 1: Umum</option>
                                        <option value="2">Kategori 2: Pelatih</option>
                                        <option value="3">Kategori 3: Private</option>
                                    </select>
                                    <div class="invalid-feedback">Kategori Customer wajib dipilih!</div>
                                </div>

                                {{-- Catatan --}}
                                <div class="col-12">
                                    <label for="edit_catatan" class="form-label fw-semibold">Catatan</label>
                                    <textarea name="catatan" id="edit_catatan" class="form-control" rows="2"
                                        placeholder="Catatan tambahan (maks 255 karakter)"></textarea>
                                </div>

                                <hr class="mt-4 mb-0">

                                {{-- Wrapper Masa Berlaku (Hanya muncul jika Kategori = 2) --}}
                                <div class="row g-4" id="edit_masa_berlaku_wrapper" style="display: none;">
                                    <div class="col-12">
                                        <h6 class="fw-bold text-primary">Detail Masa Berlaku (Khusus Pelatih)</h6>
                                    </div>

                                    {{-- Awal Masa Berlaku --}}
                                    <div class="col-md-6">
                                        <label for="edit_awal_masa_berlaku" class="form-label fw-semibold">Awal Masa
                                            Berlaku</label>
                                        <input type="date" name="awal_masa_berlaku" id="edit_awal_masa_berlaku"
                                            class="form-control">
                                        <div class="invalid-feedback">Awal Masa Berlaku wajib diisi untuk Pelatih!</div>
                                    </div>

                                    {{-- Akhir Masa Berlaku --}}
                                    <div class="col-md-6">
                                        <label for="edit_akhir_masa_berlaku" class="form-label fw-semibold">Akhir Masa
                                            Berlaku</label>
                                        <input type="date" name="akhir_masa_berlaku" id="edit_akhir_masa_berlaku"
                                            class="form-control">
                                        <div class="invalid-feedback">Akhir Masa Berlaku wajib diisi untuk Pelatih!</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>

                {{-- Footer --}}
                <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" form="formEditCustomer" class="btn btn-warning text-white px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>

            </div>
        </div>
    </div>




    {{-- Modal Delete --}}
    <div id="confirmDeleteModal" class="closecashier-modal">
        <div class="closecashier-modal-content">
            <h2>Hapus Data Customer</h2>
            <div class="closecashier-body">
                <p id="deleteCustomerInfo">Apakah Anda yakin ingin menghapus Data ini?</p>
            </div>
            <div class="closecashier-footer">
                <button id="btnCancelDelete" class="btn-danger">Batal</button>
                <form id="deleteCustomerForm" method="POST" style="display:inline;">
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
                    Data Customer Berhasil Dihapus!
                @elseif (session('action') === 'edit')
                    Data Customer Berhasil Diedit!
                @else
                    Data Customer Berhasil Ditambahkan!
                @endif
            </h3>
            <p class="success-message">
                @if (session('action') === 'delete')
                    Data Customer telah dihapus dari sistem.
                @elseif (session('action') === 'edit')
                    Data Customer telah berhasil diperbarui.
                @else
                    Data Customer baru telah berhasil disimpan.
                @endif
            </p>

        </div>
    </div>


    <script>
        // Logic code untuk kondisi kategory customer == 2 (Add Customer)
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('add_kategory_customer');
            const masaBerlakuWrapper = document.getElementById('masa_berlaku_wrapper');
            const awalMasaInput = document.getElementById('add_awal_masa_berlaku');
            const akhirMasaInput = document.getElementById('add_akhir_masa_berlaku');

            function toggleMasaBerlakuFields() {
                const isCoach = categorySelect.value === '2';

                if (isCoach) {
                    masaBerlakuWrapper.style.display = 'flex';

                    awalMasaInput.setAttribute('required', 'required');
                    akhirMasaInput.setAttribute('required', 'required');
                } else {
                    masaBerlakuWrapper.style.display = 'none';
                    awalMasaInput.removeAttribute('required');
                    akhirMasaInput.removeAttribute('required');
                }
            }

            if (categorySelect) {
                categorySelect.addEventListener('change', toggleMasaBerlakuFields);
            }
            toggleMasaBerlakuFields();
        });

        const formatDate = (dateString) => {
            if (!dateString) return '';

            if (dateString.length > 10) {
                dateString = dateString.substring(0, 10);
            }

            const date = new Date(dateString);

            if (isNaN(date.getTime())) {
                return '';
            }

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        };

        // --- Fungsi Utama untuk Membuka Modal Edit Customer ---
        async function openEditModal(id) {
            try {
                const response = await fetch(`/get-customer/${id}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const {
                    customer,
                    clubhouses
                } = await response.json();

                const form = document.getElementById('formEditCustomer');
                const selectClubhouse = document.getElementById('edit_id_club_renang');
                const selectKategory = document.getElementById('edit_kategory_customer');
                const masaBerlakuWrapper = document.getElementById('edit_masa_berlaku_wrapper');
                const awalMasaInput = document.getElementById('edit_awal_masa_berlaku');
                const akhirMasaInput = document.getElementById('edit_akhir_masa_berlaku');

                form.action = `/edit-customer/${id}`;
                document.getElementById('edit_customer_id').value = customer.id;

                document.getElementById('edit_name').value = customer.name ?? '';
                document.getElementById('edit_phone').value = customer.phone ?? '';
                document.getElementById('edit_dob').value = formatDate(customer.dob);
                document.getElementById('edit_catatan').value = customer.catatan ?? '';

                document.getElementById('edit_tipe_customer').value = customer.tipe_customer;
                selectKategory.value = customer.kategory_customer;


                selectClubhouse.innerHTML = '';
                let optionsHtml = '<option value="" disabled>Pilih Clubhouse</option>';

                clubhouses.forEach(cb => {
                    const isSelected = cb.id === customer.id_club_renang || cb.id === customer.clubhouse_id;
                    optionsHtml +=
                        `<option value="${cb.id}" ${isSelected ? 'selected' : ''}>${cb.name}</option>`;
                });

                selectClubhouse.innerHTML = optionsHtml;

                function toggleMasaBerlakuFields() {
                    const isCoach = selectKategory.value === '2';

                    if (isCoach) {
                        masaBerlakuWrapper.style.display = 'flex';
                        awalMasaInput.setAttribute('required', 'required');
                        akhirMasaInput.setAttribute('required', 'required');

                        awalMasaInput.value = formatDate(customer.awal_masa_berlaku);
                        akhirMasaInput.value = formatDate(customer.akhir_masa_berlaku);

                    } else {
                        masaBerlakuWrapper.style.display = 'none';
                        awalMasaInput.removeAttribute('required');
                        akhirMasaInput.removeAttribute('required');
                    }
                }
                toggleMasaBerlakuFields();

                selectKategory.onchange = toggleMasaBerlakuFields;

                const editModal = new bootstrap.Modal(document.getElementById('modalEditCustomer'));
                editModal.show();
            } catch (error) {
                console.error('Error saat memuat data Customer:', error);
                alert('Terjadi kesalahan saat memuat data customer. Cek konsol untuk detail.');
            }
        }

        const confirmModal = document.getElementById('confirmDeleteModal');
        const deleteCustomerForm = document.getElementById('deleteCustomerForm');
        const deleteCustomerInfo = document.getElementById('deleteCustomerInfo');
        const cancelBtn = document.getElementById('btnCancelDelete');

        function openConfirmModal(id, name) {
            confirmModal.style.display = 'flex';
            deleteCustomerInfo.innerHTML =
                `<p>Apakah Anda yakin ingin menghapus Data ini <strong>${name}</strong>?</p>`;
            deleteCustomerForm.action = `/delete-customer/${id}`;
        }

        cancelBtn.addEventListener('click', () => {
            confirmModal.style.display = 'none';
        });

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
