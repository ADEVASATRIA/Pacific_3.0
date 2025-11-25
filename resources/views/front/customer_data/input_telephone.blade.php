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
        let customers = [];

        // Auto-fill nama ketika phone diisi
        document.getElementById('phone').addEventListener('input', function(e) {
            const phone = e.target.value;

            if (phone.length >= 10) {
                fetch(`/api/customer/search-by-phone?phone=${phone}`)
                    .then(response => response.json())
                    .then(data => {
                        // console.log('[DEBUG] 🔍 Hasil search-by-phone:', data);
                        if (data.success && data.customer) {
                            document.getElementById('name').value = data.customer.name;
                        } else {
                            document.getElementById('name').value = '';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            } else {
                document.getElementById('name').value = '';
            }
        });

        // Fungsi untuk membuka modal
        function openContactModal() {
            document.getElementById('contactModal').style.display = 'flex';
            loadCustomers();
        }

        // Fungsi untuk menutup modal
        function closeContactModal() {
            document.getElementById('contactModal').style.display = 'none';
        }

        // Fungsi escape untuk menghindari karakter bermasalah di HTML
        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Load semua customers
        function loadCustomers() {
            fetch('/api/customer/all')
                .then(response => response.json())
                .then(data => {
                    // console.log('[DEBUG] 📦 Data customer dari API:', data);

                    if (!data.success) {
                        console.warn('[DEBUG] ⚠️ Gagal ambil data customer!');
                        document.getElementById('customerList').innerHTML =
                        '<div class="error">Gagal memuat data</div>';
                        return;
                    }

                    customers = data.customers;

                    // Debug tiap customer
                    customers.forEach((c, i) => {
                        if (isNaN(parseInt(c.phone))) {
                            console.warn(`[DEBUG] ⚠️ Data aneh di index ${i}: phone bukan angka`, c);
                        }
                    });

                    displayCustomers(customers);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('customerList').innerHTML = '<div class="error">Gagal memuat data</div>';
                });
        }

        // Display customers di modal
        function displayCustomers(customerList) {
            const listContainer = document.getElementById('customerList');

            if (!customerList || customerList.length === 0) {
                listContainer.innerHTML = '<div class="no-data">Tidak ada data customer</div>';
                return;
            }

            let html = '';
            customerList.forEach(customer => {
                html += `
            <div class="customer-item" 
                 data-phone="${escapeHtml(customer.phone)}" 
                 data-name="${escapeHtml(customer.name)}"
                 onclick="selectCustomerFromDataset(this)">
                <div class="customer-info">
                    <div class="customer-name">${escapeHtml(customer.name)}</div>
                    <div class="customer-phone">${escapeHtml(customer.phone)}</div>
                </div>
                <div class="customer-select">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </div>
            </div>
        `;
            });

            listContainer.innerHTML = html;
        }

        // Search customers
        function searchCustomers() {
            const searchTerm = document.getElementById('searchContact').value.toLowerCase();

            const filtered = customers.filter(customer => {
                return (
                    customer.name.toLowerCase().includes(searchTerm) ||
                    customer.phone.includes(searchTerm)
                );
            });

            displayCustomers(filtered);
        }

        // Select customer via dataset (fix bug nama & phone ketukar)
        function selectCustomerFromDataset(element) {
            const phone = element.dataset.phone;
            const name = element.dataset.name;

            // console.log('[DEBUG] ✅ Customer dipilih:', {
            //     name,
            //     phone
            // });

            // Deteksi kemungkinan terbalik
            if (/^\d+$/.test(name) && !/^\d+$/.test(phone)) {
                console.warn('[DEBUG] ⚠️ Deteksi data terbalik! Nama berisi angka, phone berisi teks:', {
                    name,
                    phone
                });
            }

            document.getElementById('phone').value = phone;
            document.getElementById('name').value = name;

            closeContactModal();
        }

        // Close modal ketika klik di luar area
        window.onclick = function(event) {
            const modal = document.getElementById('contactModal');
            if (event.target === modal) {
                closeContactModal();
            }
        }
    </script>
@endsection
