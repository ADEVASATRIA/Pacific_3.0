@extends('main.second_blank')
@section('content')
    @vite('resources/css/front/beli_ticket.css')
    <div class="form-wrapper">
        <div class="form-card">
            <h2 class="form-title text-center">Beli Tiket</h2>
            <form action="{{ route('check_customer') }}" method="POST" class="ticket-form">
                @csrf
                <div class="form-group">
                    <label for="phone">No Telephone</label>
                    <div class="input-with-button">
                        <input type="text" id="phone" name="phone" required>
                        <button type="button" class="btn-contact-book" onclick="openContactModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-footer">
                    Belum terdaftar? <a href="{{ route('registrasi_new_customer') }}">Registrasi disini</a>
                </div>
                <button type="submit" class="btn-submit">Submit</button>
            </form>
        </div>
    </div>

    <!-- Modal Contact Book -->
    <div id="contactModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Pilih Customer</h3>
                <button type="button" class="btn-close" onclick="closeContactModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="search-box">
                    <input type="text" id="searchContact" placeholder="Cari nama atau nomor telephone..."
                        onkeyup="searchCustomers()">
                </div>
                <div id="customerList" class="customer-list">
                    <div class="loading">Memuat data...</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ─── State ───────────────────────────────────────────────────────────────
        let customers        = [];   // cache hasil fetch terakhir
        let customersLoaded  = false; // true setelah fetch awal (tanpa keyword) berhasil
        let searchTimer      = null;  // referensi timer debounce

        // ─── Auto-fill nama dari nomor telepon ────────────────────────────────────
        document.getElementById('phone').addEventListener('input', function (e) {
            const phone = e.target.value;
            if (phone.length >= 10) {
                fetch(`/api/customer/search-by-phone?phone=${encodeURIComponent(phone)}`)
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('name').value =
                            (data.success && data.customer) ? data.customer.name : '';
                    })
                    .catch(() => {});
            } else {
                document.getElementById('name').value = '';
            }
        });

        // ─── Modal open / close ───────────────────────────────────────────────────
        function openContactModal() {
            document.getElementById('contactModal').style.display = 'flex';
            document.getElementById('searchContact').value = '';

            if (customersLoaded) {
                // Data sudah di-cache, tampilkan langsung tanpa fetch ulang
                displayCustomers(customers);
            } else {
                fetchCustomers('');
            }
        }

        function closeContactModal() {
            document.getElementById('contactModal').style.display = 'none';
        }

        // ─── Search dengan debounce 300 ms ────────────────────────────────────────
        function searchCustomers() {
            const term = document.getElementById('searchContact').value.trim();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => fetchCustomers(term), 300);
        }

        // ─── Fetch data dari server (server-side search + limit) ──────────────────
        function fetchCustomers(search) {
            const listContainer = document.getElementById('customerList');
            listContainer.innerHTML = '<div class="loading">Memuat data...</div>';

            fetch(`/api/customer/all?search=${encodeURIComponent(search)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        listContainer.innerHTML = '<div class="error">Gagal memuat data</div>';
                        return;
                    }
                    customers = data.customers;
                    // Tandai cache hanya untuk load awal tanpa keyword
                    if (search === '') customersLoaded = true;
                    displayCustomers(customers);
                })
                .catch(() => {
                    listContainer.innerHTML = '<div class="error">Gagal memuat data</div>';
                });
        }

        // ─── Render list dengan DocumentFragment (menghindari reflow berulang) ────
        function displayCustomers(customerList) {
            const listContainer = document.getElementById('customerList');

            if (!customerList || customerList.length === 0) {
                listContainer.innerHTML = '<div class="no-data">Tidak ada data customer</div>';
                return;
            }

            const fragment = document.createDocumentFragment();

            customerList.forEach(customer => {
                const item = document.createElement('div');
                item.className = 'customer-item';
                item.dataset.phone = customer.phone || '';
                item.dataset.name  = customer.name  || '';
                item.addEventListener('click', function () {
                    selectCustomerFromDataset(this);
                });
                item.innerHTML = `
                    <div class="customer-info">
                        <div class="customer-name">${escapeHtml(customer.name)}</div>
                        <div class="customer-phone">${escapeHtml(customer.phone)}</div>
                    </div>
                    <div class="customer-select">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                `;
                fragment.appendChild(item);
            });

            listContainer.innerHTML = '';       // satu reflow di sini …
            listContainer.appendChild(fragment); // … lalu satu paint selesai
        }

        // ─── Pilih customer dari modal ────────────────────────────────────────────
        function selectCustomerFromDataset(element) {
            document.getElementById('phone').value = element.dataset.phone;
            document.getElementById('name').value  = element.dataset.name;
            closeContactModal();
        }

        // ─── Tutup modal ketika klik di luar area ─────────────────────────────────
        window.addEventListener('click', function (event) {
            const modal = document.getElementById('contactModal');
            if (event.target === modal) closeContactModal();
        });

        // ─── Escape helper ────────────────────────────────────────────────────────
        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g,  '&amp;')
                .replace(/</g,  '&lt;')
                .replace(/>/g,  '&gt;')
                .replace(/"/g,  '&quot;')
                .replace(/'/g,  '&#039;');
        }

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

                setTimeout(() => alertDiv.classList.add('show'), 50);
                setTimeout(() => {
                    alertDiv.classList.remove('show');
                    setTimeout(() => alertDiv.remove(), 300);
                }, 20000);
            }

            @if ($errors->any())
                showAlert('error', '{{ addslashes($errors->first()) }}');
            @endif

            @if (session('alert_message'))
                showAlert('{{ session('alert_type', 'error') }}', '{{ session('alert_message') }}');
            @endif

            @if (session('redirect_to_register'))
                setTimeout(function() {
                    window.location.href = '{{ route('registrasi_new_customer') }}';
                }, 2000);
            @endif
        });
    </script>
@endsection
