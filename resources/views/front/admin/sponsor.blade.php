@extends('front.admin.index')

@section('title', 'Data Sponsor Pacific')
@section('page-title', 'Daftar Sponsor Pacific')
@vite('resources/css/admin/member.css', 'resources/css/admin/close-modal.css')
@push('styles')
    <style>
        /* Styling Tambahan untuk CRUD */
        .table-wrap {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .top-action-bar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .sponsor-image-preview {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            object-fit: cover;
            margin-top: 10px;
            border: 1px solid #ddd;
        }

        .action-buttons button,
        .action-buttons a {
            padding: 4px 8px;
            font-size: 12px;
            margin-right: 5px;
            border-radius: 4px;
            cursor: pointer;
        }

        .modal {
            display: none;
            /* Sembunyikan secara default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content h2 {
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .detail-item {
            margin-bottom: 10px;
        }

        .detail-item strong {
            display: inline-block;
            width: 120px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.active {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-badge.inactive {
            background-color: #f8d7da;
            color: #842029;
        }
    </style>
@endpush

{{-- Bagian Kontrol Atas --}}
@section('top-controls')
    <div class="top-action-bar">
        <!-- Tombol Tambah Sponsor -->
        <button id="btnAddSponsor" class="btn primary">
            <i data-lucide="plus" style="width:16px; height:16px; margin-right: 5px;"></i>
            Tambah Sponsor
        </button>
    </div>
@endsection

@section('content')
    {{-- Notifikasi Sukses dari redirect controller --}}
    @if (session('success'))
        <div class="alert success" style="margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <section class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Gambar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sponsors as $sponsor)
                    <tr data-sponsor-id="{{ $sponsor->id }}">
                        <td>{{ $sponsor->name }}</td>
                        <td>
                            @if ($sponsor->image)
                                {{-- FIX: Menggunakan nama kelas yang sepenuhnya terkualifikasi untuk Storage::url() --}}
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($sponsor->image) }}"
                                    alt="Gambar Sponsor"
                                    style="height: 50px; width: 50px; object-fit: cover; border-radius: 4px;">
                            @else
                                <span class="small text-muted">Tidak ada gambar</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $sponsor->status == 1 ? 'active' : 'inactive' }}">
                                {{ $sponsor->status == 1 ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="action-buttons">
                            <button class="btn info btn-detail" data-id="{{ $sponsor->id }}">Detail</button>
                            <button class="btn warning btn-edit" data-id="{{ $sponsor->id }}">Edit</button>
                            <button class="btn danger btn-delete" data-id="{{ $sponsor->id }}"
                                data-name="{{ $sponsor->name }}">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="small" style="text-align: center;">Belum ada sponsor</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-wrapper">
            <ul class="pagination">
                {{-- Tombol Previous --}}
                @if ($sponsors->onFirstPage())
                    <li class="disabled"><span>&laquo; Prev</span></li>
                @else
                    <li><a href="{{ $sponsors->previousPageUrl() }}">&laquo; Prev</a></li>
                @endif

                {{-- Nomor Halaman --}}
                @foreach ($sponsors->getUrlRange(1, $sponsors->lastPage()) as $page => $url)
                    @if ($page == $sponsors->currentPage())
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach

                {{-- Tombol Next --}}
                @if ($sponsors->hasMorePages())
                    <li><a href="{{ $sponsors->nextPageUrl() }}">Next &raquo;</a></li>
                @else
                    <li class="disabled"><span>Next &raquo;</span></li>
                @endif
            </ul>
        </div>
    </section>
@endsection

<!-- 🔹 Modal Tambah/Edit Sponsor -->
<div id="sponsorModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle">Tambah Sponsor Baru</h2>
        <form id="sponsorForm" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Hidden field untuk menentukan method dan ID (diisi via JS) -->
            <input type="hidden" name="sponsor_id" id="sponsorId">

            <div class="form-group">
                <label for="name">Nama Sponsor</label>
                <input type="text" name="name" id="name" required>
            </div>

            <div class="form-group">
                <label for="image">Gambar Sponsor (Max 2MB)</label>
                <input type="file" name="image" id="image">
                <small class="text-muted" id="imageHint">Wajib diisi saat tambah, kosongkan saat edit.</small>
            </div>

            <div class="form-group" id="currentImageContainer" style="display: none;">
                <label>Gambar Saat Ini:</label>
                <img id="currentImage" class="sponsor-image-preview" src="" alt="Gambar Saat Ini">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" required>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn danger" id="btnCloseSponsorModal">Batal</button>
                <button type="submit" class="btn primary" id="btnSubmitSponsor">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- 🔹 Modal Detail Sponsor -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <h2>Detail Sponsor</h2>
        <div id="detailContent">
            <div class="detail-item"><strong>ID:</strong> <span id="detailId"></span></div>
            <div class="detail-item"><strong>Nama:</strong> <span id="detailName"></span></div>
            <div class="detail-item"><strong>Status:</strong> <span id="detailStatus"></span></div>
            <div class="detail-item"><strong>Dibuat:</strong> <span id="detailCreatedAt"></span></div>
            <div class="detail-item">
                <strong>Gambar:</strong>
                <img id="detailImage" class="sponsor-image-preview" src="" alt="Gambar Sponsor">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn secondary" id="btnCloseDetailModal">Tutup</button>
        </div>
    </div>
</div>

{{-- ====================== --}}
{{-- ✅ Modal Konfirmasi Delete --}}
{{-- ====================== --}}
<div id="confirmDeleteModal" class="closecashier-modal">
    <div class="closecashier-modal-content">
        <h2>Hapus Sponsor</h2>
        <div class="closecashier-body">
            <p id="deleteSponsorInfo">Apakah Anda yakin ingin menghapus Data ini?</p>
        </div>
        <div class="closecashier-footer">
            <button id="btnCancelDelete" class="btn-danger">Batal</button>
            <form id="deleteSponsorForm" method="POST" style="display:inline;">
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
                Sponsor Berhasil Dihapus!
            @elseif (session('action') === 'edit')
                Sponsor Berhasil Diedit!
            @else
                Sponsor Berhasil Ditambahkan!
            @endif
        </h3>
        <p class="success-message">
            @if (session('action') === 'delete')
                Data Sponsor telah dihapus dari sistem.
            @elseif (session('action') === 'edit')
                Data Sponsor telah berhasil diperbarui.
            @else
                Data Sponsor baru telah berhasil disimpan.
            @endif
        </p>

    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==============================
            // 🔹 Elemen Modal
            // ==============================
            const modal = document.getElementById('sponsorModal');
            const detailModal = document.getElementById('detailModal');
            const confirmDeleteModal = document.getElementById('confirmDeleteModal');
            const successModal = document.getElementById('successModal');

            // ==============================
            // 🔹 Elemen Form & Field
            // ==============================
            const form = document.getElementById('sponsorForm');
            const modalTitle = document.getElementById('modalTitle');
            const sponsorId = document.getElementById('sponsorId');
            const imageInput = document.getElementById('image');
            const imageHint = document.getElementById('imageHint');
            const currentImageContainer = document.getElementById('currentImageContainer');
            const currentImage = document.getElementById('currentImage');
            const submitBtn = document.getElementById('btnSubmitSponsor');

            const deleteForm = document.getElementById('deleteSponsorForm');
            const deleteSponsorInfo = document.getElementById('deleteSponsorInfo');
            const btnCancelDelete = document.getElementById('btnCancelDelete');

            const baseApiUrl = "{{ url('/admin/sponsor') }}";

            // ==============================
            // 🔹 Tombol Modal & Penutup
            // ==============================
            const btnAdd = document.getElementById('btnAddSponsor');
            if (btnAdd) btnAdd.addEventListener('click', () => openModal('create'));
            document.getElementById('btnCloseSponsorModal').addEventListener('click', () => modal.style.display =
                'none');
            document.getElementById('btnCloseDetailModal').addEventListener('click', () => detailModal.style
                .display = 'none');
            btnCancelDelete.addEventListener('click', () => confirmDeleteModal.style.display = 'none');

            // Klik luar area = tutup modal
            [modal, detailModal, confirmDeleteModal].forEach(m =>
                m.addEventListener('click', e => {
                    if (e.target === m) m.style.display = 'none';
                })
            );

            // ==============================
            // 🔹 Fungsi Modal Form
            // ==============================
            function openModal(mode, data = {}) {
                form.reset();
                currentImageContainer.style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan';

                if (mode === 'create') {
                    modalTitle.textContent = 'Tambah Sponsor Baru';
                    form.action = "{{ route('admin.sponsor.store') }}";
                    sponsorId.value = '';
                    imageInput.required = true;
                    imageHint.textContent = 'Wajib diisi untuk sponsor baru.';
                } else if (mode === 'edit') {
                    modalTitle.textContent = 'Edit Sponsor: ' + data.name;
                    form.action = `${baseApiUrl}/${data.id}/update`;
                    sponsorId.value = data.id;
                    document.getElementById('name').value = data.name;
                    document.getElementById('status').value = data.status;
                    imageInput.required = false;
                    imageHint.textContent = 'Kosongkan jika tidak ingin mengubah gambar.';
                    if (data.image_url) {
                        currentImage.src = data.image_url;
                        currentImageContainer.style.display = 'block';
                    }
                }
                modal.style.display = 'flex';
            }

            // ==============================
            // 🔹 Ambil Detail Sponsor
            // ==============================
            async function fetchSponsorDetails(id) {
                try {
                    const response = await fetch(`${baseApiUrl}/${id}`);
                    if (!response.ok) throw new Error('Gagal mengambil data sponsor.');
                    const data = await response.json();

                    document.getElementById('detailId').textContent = data.id || '-';
                    document.getElementById('detailName').textContent = data.name || '-';
                    document.getElementById('detailStatus').textContent = data.status == 1 ? 'Aktif' :
                        'Nonaktif';

                    const formattedDate = data.created_at ?
                        new Date(data.created_at).toLocaleString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit',
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }) : '-';
                    document.getElementById('detailCreatedAt').textContent = formattedDate;

                    const detailImage = document.getElementById('detailImage');
                    detailImage.src = data.image_url ||
                        'https://placehold.co/100x100/eeeeee/333333?text=No+Image';

                    detailModal.style.display = 'flex';
                } catch (error) {
                    console.error(error);
                    alert('Gagal memuat detail sponsor.');
                }
            }

            // ==============================
            // 🔹 Buka Modal Delete
            // ==============================
            function openDeleteModal(id, name) {
                deleteForm.action = `${baseApiUrl}/${id}`;
                deleteSponsorInfo.textContent = `Apakah Anda yakin ingin menghapus Sponsor "${name}"?`;
                confirmDeleteModal.style.display = 'flex';
            }

            // ==============================
            // 🔹 Delegasi Tombol Table
            // ==============================
            document.querySelector('tbody').addEventListener('click', async (e) => {
                const btn = e.target.closest('button');
                if (!btn || !btn.dataset.id) return;
                const id = btn.dataset.id;

                if (btn.classList.contains('btn-detail')) {
                    await fetchSponsorDetails(id);
                } else if (btn.classList.contains('btn-edit')) {
                    const response = await fetch(`${baseApiUrl}/${id}`);
                    if (!response.ok) return alert('Gagal memuat data edit.');
                    const data = await response.json();
                    openModal('edit', data);
                } else if (btn.classList.contains('btn-delete')) {
                    const name = btn.dataset.name || 'Sponsor';
                    openDeleteModal(id, name);
                }
            });

            // ==============================
            // 🔹 Validasi Gambar Sebelum Submit
            // ==============================
            form.addEventListener('submit', function(e) {
                const file = imageInput.files[0];
                if (file && file.size > 2048 * 1024) {
                    e.preventDefault();
                    alert('Ukuran gambar tidak boleh lebih dari 2MB.');
                    return;
                }
                submitBtn.disabled = true;
                submitBtn.textContent = 'Menyimpan...';
            });

            // ==============================
            // 🔹 Modal Sukses Otomatis
            // ==============================
            @if (session('success'))
                window.addEventListener('load', () => {
                    successModal.style.display = 'flex';
                    setTimeout(() => {
                        successModal.style.display = 'none';
                    }, 2500);
                });
            @endif
        });
    </script>
@endpush
