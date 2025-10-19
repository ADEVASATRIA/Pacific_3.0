document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('closeCashierModal');
    const successModal = document.getElementById('successModal');

    // Ambil semua elemen input yang relevan
    const saldoDisplay = document.getElementById('saldo_akhir_display');
    const saldoHidden = document.getElementById('saldo_akhir');
    
    // Elemen baru
    const fnbDisplay = document.getElementById('fnb_balance_display');
    const fnbHidden = document.getElementById('fnb_balance');
    const minusDisplay = document.getElementById('minus_balance_display');
    const minusHidden = document.getElementById('minus_balance');


    // 🔹 Tombol buka modal
    const btnOpen = document.getElementById('btnOpenCloseModal');
    if (btnOpen) {
        btnOpen.addEventListener('click', (e) => {
            e.preventDefault();
            modal.style.display = 'flex';
            const timeEl = document.getElementById('closeCashierTime');
            if (timeEl) timeEl.innerText = new Date().toLocaleString('id-ID');
        });
    }

    // 🔹 Tombol tutup modal
    const btnClose = document.getElementById('btnCloseModal');
    if (btnClose) {
        btnClose.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    // 🔹 Tutup modal saat klik di luar content
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
    });

    // 🔹 Tombol export report
    const btnExport = document.getElementById('btnExportReport');
    if (btnExport) {
        btnExport.addEventListener('click', () => {
            window.location.href = window.CashierRoutes.exportReport;
        });
    }

    // === Helper format Rp ===
    const formatRupiah = (num) => {
        const str = num.toString().replace(/[^0-9]/g, '');
        if (str === '') return '';
        // Ubah angka menjadi string, lalu format dengan titik sebagai pemisah ribuan
        return 'Rp. ' + str.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };
    const toNumber = (str) => parseInt(str.replace(/[^0-9]/g, '')) || 0;

    // === Fungsi untuk mengotomatisasi input Rupiah ===
    /**
     * @param {HTMLInputElement} displayInput - Input untuk menampilkan format Rupiah.
     * @param {HTMLInputElement} hiddenInput - Input tersembunyi untuk menyimpan nilai murni (angka).
     */
    const setupRupiahInput = (displayInput, hiddenInput) => {
        if (displayInput && hiddenInput) {
            displayInput.addEventListener('input', function () {
                // Hapus semua karakter kecuali angka
                const raw = this.value.replace(/[^0-9]/g, '');
                
                if (raw === '') {
                    this.value = '';
                    hiddenInput.value = 0;
                    return;
                }

                // Format tampilan
                this.value = formatRupiah(raw);

                // Simpan nilai murni
                hiddenInput.value = parseInt(raw);
            });
        }
    };

    // Terapkan fungsi format ke semua input saldo
    setupRupiahInput(saldoDisplay, saldoHidden);
    setupRupiahInput(fnbDisplay, fnbHidden);
    setupRupiahInput(minusDisplay, minusHidden);


    // 🔹 Tombol tutup kasir & logout
    const btnProcess = document.getElementById('btnProcessClose');
    if (btnProcess) {
        btnProcess.addEventListener('click', async () => {
            // Ambil nilai murni (dari hidden fields)
            const saldoAkhir = toNumber(saldoDisplay.value);
            const fnbBalance = toNumber(fnbDisplay.value);
            const minusBalance = toNumber(minusDisplay.value);

            // Validasi sederhana (Anda bisa sesuaikan validasi ini)
            if (saldoAkhir <= 0) {
                // Menggunakan console.error dan custom UI/modal daripada alert()
                console.error('Harap isi Saldo Akhir (Total Fisik Kas) dengan benar.');
                // Lakukan penanganan error UI (misalnya: tampilkan pesan error di modal)
                return;
            }

            // Disable button saat proses
            btnProcess.disabled = true;
            btnProcess.textContent = 'Memproses...';

            try {
                const payload = {
                    saldo_akhir: saldoAkhir,
                    fnb_balance: fnbBalance, // Data baru
                    minus_balance: minusBalance // Data baru
                };

                const response = await fetch(window.CashierRoutes.processClose, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.CsrfToken,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload) // Mengirim payload lengkap
                });

                const data = await response.json();

                if (data.success) {
                    // Tutup modal utama
                    modal.style.display = 'none';

                    // Tampilkan modal success
                    successModal.style.display = 'flex';

                    // Redirect setelah 2 detik
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    console.error('Terjadi kesalahan saat menutup kasir:', data.message || 'Unknown error');
                    // Ganti alert() dengan penanganan UI/modal error jika Anda sudah menyiapkannya
                    btnProcess.disabled = false;
                    btnProcess.textContent = 'Tutup Kasir & Logout';
                }
            } catch (err) {
                console.error('Terjadi kesalahan jaringan/fetch:', err);
                // Ganti alert() dengan penanganan UI/modal error jika Anda sudah menyiapkannya
                btnProcess.disabled = false;
                btnProcess.textContent = 'Tutup Kasir & Logout';
            }
        });
    }
});
