document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('closeCashierModal');
    const successModal = document.getElementById('successModal');
    const saldoDisplay = document.getElementById('saldo_akhir_display');
    const saldoHidden = document.getElementById('saldo_akhir');

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
        return 'Rp. ' + str.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };
    const toNumber = (str) => parseInt(str.replace(/[^0-9]/g, '')) || 0;

    // === Format saldo akhir otomatis ===
    if (saldoDisplay) {
        saldoDisplay.addEventListener('input', function () {
            const raw = this.value.replace(/[^0-9]/g, '');
            if (raw === '') {
                this.value = '';
                saldoHidden.value = 0;
                return;
            }

            // Format tampilan
            this.value = formatRupiah(raw);

            // Simpan nilai murni
            saldoHidden.value = parseInt(raw);
        });
    }

    // 🔹 Tombol tutup kasir & logout
    const btnProcess = document.getElementById('btnProcessClose');
    if (btnProcess) {
        btnProcess.addEventListener('click', async () => {
            const saldoAkhir = toNumber(saldoDisplay.value);

            if (!saldoAkhir || saldoAkhir <= 0) {
                alert('Harap isi saldo akhir dengan benar.');
                return;
            }

            // Disable button saat proses
            btnProcess.disabled = true;
            btnProcess.textContent = 'Memproses...';

            try {
                const response = await fetch(window.CashierRoutes.processClose, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.CsrfToken,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ saldo_akhir: saldoAkhir })
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
                    alert('Terjadi kesalahan saat menutup kasir.');
                    btnProcess.disabled = false;
                    btnProcess.textContent = 'Tutup Kasir & Logout';
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
                btnProcess.disabled = false;
                btnProcess.textContent = 'Tutup Kasir & Logout';
            }
        });
    }
});
