@extends('main.back_blank')
@section('title', 'Data Member')
@vite('resources/css/admin/close-modal.css')

@section('content')
    <div class="promo-page">
        <h2 class="page-title">Data Member</h2>
        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('member') }}" class="filter-form flex items-end gap-4 flex-wrap">

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

                    <div class="col-md-auto">
                        <label for="dob" class="form-label block text-sm font-medium text-gray-700">Tgl Lahir</label>
                        <input type="date" name="dob" id="dob"
                            class="form-control mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            value="{{ request('dob') }}">
                    </div>
                </div>

                <div class="row g-3 items-end">
                    <div class="col-md-auto">
                        <label for="awal_masa" class="form-label block text-sm font-medium text-gray-700">Awal Masa
                            Berlaku</label>
                        <input type="date" name="awal_masa" id="awal_masa"
                            class="form-control mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            value="{{ request('awal_masa') }}">
                    </div>

                    <div class="col-md-auto">
                        <label for="akhir_masa" class="form-label block text-sm font-medium text-gray-700">Akhir Masa
                            Berlaku</label>
                        <input type="date" name="akhir_masa" id="akhir_masa"
                            class="form-control mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            value="{{ request('akhir_masa') }}">
                    </div>

                    <div class="col-md-auto">
                        <label for="status" class="form-label block text-sm font-medium text-gray-700">Status
                            Member</label>
                        <select name="status" id="status"
                            class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kedaluwarsa
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group flex items-end">
                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700"
                            data-bs-toggle="modal" data-bs-target="#modalTambahMember">
                            <i class="fas fa-plus me-1"></i> Tambah Data Member
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-section mt-4">
            <table class="table w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center">Nomer Telephone</th>
                        <th class="text-center">Nama</th> {{-- Tambahkan kolom Nama jika $member->name ada --}}
                        <th class="text-center">Awal Masa Berlaku</th>
                        <th class="text-center">Akhir masa Berlaku</th>
                        <th class="text-center">Tanggal Lahir</th>
                        <th class="text-center">Member ID</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                        <tr>
                            <td>{{ $member->phone }}</td>
                            <td>{{ $member->name }}</td>
                            <td class="text-center">
                                {{ $member->tiketTerbaru?->date_start ? \Carbon\Carbon::parse($member->tiketTerbaru->date_start)->format('d M Y') : '-' }}
                            </td>
                            <td class="text-center">
                                {{ $member->tiketTerbaru?->date_end ? \Carbon\Carbon::parse($member->tiketTerbaru->date_end)->format('d M Y') : '-' }}
                            </td>
                            <td class="text-center">
                                @if (!empty($member->dob))
                                    {{ \Carbon\Carbon::parse($member->dob)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if (!empty($member->member_id))
                                    {{ $member->member_id }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm" onclick="openEditModal({{ $member->id }})">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="openConfirmModal({{ $member->id }}, '{{ $member->name }}')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 py-3">
                                Tidak ada data member
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($members->hasPages())
                <div class="mt-4 flex justify-center">
                    {{ $members->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Edit Member --}}
    <div class="modal fade" id="modalEditMember" tabindex="-1" aria-labelledby="modalEditMemberLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-semibold" id="modalEditMemberLabel">
                        <i class="fas fa-user-edit me-2"></i>Edit Member
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light py-4">
                    <form id="formEditMember" method="POST" class="needs-validation" novalidate>
                        @csrf
                        <div class="container-fluid">
                            <div class="row g-3">

                                {{-- Nama --}}
                                <div class="col-md-6">
                                    <label for="edit_name" class="form-label fw-semibold">Nama</label>
                                    <input type="text" name="name" id="edit_name" class="form-control"
                                        placeholder="Masukkan nama member" required>
                                    <div class="invalid-feedback">Nama wajib diisi.</div>
                                </div>

                                {{-- Nomor Telepon --}}
                                <div class="col-md-6">
                                    <label for="edit_phone" class="form-label fw-semibold">Nomor Telepon</label>
                                    <input type="text" name="phone" id="edit_phone" class="form-control"
                                        placeholder="Masukkan nomor telepon">
                                </div>

                                {{-- Tanggal Lahir --}}
                                <div class="col-md-6">
                                    <label for="edit_dob" class="form-label fw-semibold">Tanggal Lahir</label>
                                    <input type="date" name="dob" id="edit_dob" class="form-control">
                                </div>

                                {{-- Member ID (Display Only, Optional) --}}
                                <div class="col-md-6">
                                    <label for="display_member_id" class="form-label fw-semibold">Member ID</label>
                                    <input type="text" id="display_member_id" class="form-control" readonly disabled>
                                </div>

                                {{-- Awal Masa Berlaku (Tiket) --}}
                                <div class="col-md-6">
                                    <label for="edit_awal_masa_berlaku" class="form-label fw-semibold">Awal Masa
                                        Berlaku</label>
                                    <input type="date" name="awal_masa_berlaku" id="edit_awal_masa_berlaku"
                                        class="form-control">
                                </div>

                                {{-- Akhir Masa Berlaku (Tiket) --}}
                                <div class="col-md-6">
                                    <label for="edit_akhir_masa_berlaku" class="form-label fw-semibold">Akhir Masa
                                        Berlaku</label>
                                    <input type="date" name="akhir_masa_berlaku" id="edit_akhir_masa_berlaku"
                                        class="form-control">
                                </div>

                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="formEditMember" class="btn btn-warning text-white px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Delete --}}
    <div id="confirmDeleteModal" class="closecashier-modal">
        <div class="closecashier-modal-content">
            <h2>Hapus Data Member</h2>
            <div class="closecashier-body">
                <p id="deleteMemberInfo">Apakah Anda yakin ingin menghapus Data ini?</p>
            </div>
            <div class="closecashier-footer">
                <button id="btnCancelDelete" class="btn-danger">Batal</button>
                <form id="deleteMemberForm" method="POST" style="display:inline;">
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
                    Data Member Berhasil Dihapus!
                @elseif (session('action') === 'edit')
                    Data Member Berhasil Diedit!
                @else
                    Data Member Berhasil Ditambahkan!
                @endif
            </h3>
            <p class="success-message">
                @if (session('action') === 'delete')
                    Data Member telah dihapus dari sistem.
                @elseif (session('action') === 'edit')
                    Data member telah berhasil diperbarui.
                @else
                    Data Member baru telah berhasil disimpan.
                @endif
            </p>

        </div>
    </div>


    <script>
        // --- Modal Edit Member ---
        async function openEditModal(id) {
            try {
                const response = await fetch(`/get-member/${id}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                // Asumsi data JSON berisi { member: {...}, ticket: {...} }
                const {
                    member,
                    ticket
                } = await response.json();
                // console.log('Member Data:', member);
                // console.log('Ticket Data:', ticket);

                const form = document.getElementById('formEditMember');
                // Set action URL untuk proses POST update
                form.action = `/edit-member/${id}`;

                // Fungsi helper untuk format tanggal (YYYY-MM-DD)
                const formatDate = (dateString) => {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.getFullYear() + '-' +
                        String(date.getMonth() + 1).padStart(2, '0') + '-' +
                        String(date.getDate()).padStart(2, '0');
                };

                // Mengisi data Customer (Member)
                document.getElementById('edit_name').value = member.name ?? '';
                document.getElementById('edit_phone').value = member.phone ?? '';
                document.getElementById('edit_dob').value = formatDate(member.dob);
                document.getElementById('display_member_id').value = member.member_id ?? '-';


                // Mengisi data Tiket Member (Awal/Akhir Masa Berlaku)
                // Nilai diambil dari objek 'ticket' yang dikembalikan controller
                if (ticket) {
                    document.getElementById('edit_awal_masa_berlaku').value = formatDate(ticket.date_start);
                    document.getElementById('edit_akhir_masa_berlaku').value = formatDate(ticket.date_end);
                } else {
                    document.getElementById('edit_awal_masa_berlaku').value = '';
                    document.getElementById('edit_akhir_masa_berlaku').value = '';
                }

                // Tampilkan modal
                const editModal = new bootstrap.Modal(document.getElementById('modalEditMember'));
                editModal.show();
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil data member.');
            }
        }
        // Logic untuk modal delete (tetap seperti sebelumnya)
        const confirmModal = document.getElementById('confirmDeleteModal');
        const deleteMemberForm = document.getElementById('deleteMemberForm');
        const deleteMemberInfo = document.getElementById('deleteMemberInfo');
        const cancelBtn = document.getElementById('btnCancelDelete');

        function openConfirmModal(id, name) {
            confirmModal.style.display = 'flex';
            deleteMemberInfo.innerHTML =
                `<p>Apakah Anda yakin ingin menghapus Data ini <strong>${name}</strong>?</p>`;
            deleteMemberForm.action = `/delete-member/${id}`;
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
