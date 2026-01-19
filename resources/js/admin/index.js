document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('closeCashierModal');
    const successModal = document.getElementById('cashierSuccessModal');

    // Ambil semua elemen input yang relevan
    const saldoDisplay = document.getElementById('saldo_akhir_display');
    const saldoHidden = document.getElementById('saldo_akhir');

    const kolamDisplay = document.getElementById('penjualan_fnb_kolam_display');
    const kolamHidden = document.getElementById('penjualan_fnb_kolam');
    const cafeDisplay = document.getElementById('penjualan_fnb_cafe_display');
    const cafeHidden = document.getElementById('penjualan_fnb_cafe');
    const cashInList = document.getElementById('cashInList');
    const cashOutList = document.getElementById('cashOutList');
    const btnAddCashInRow = document.getElementById('btnAddCashInRow');
    const btnAddCashOutRow = document.getElementById('btnAddCashOutRow');


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

    // 🔹 Tombol Print Summary
    const btnPrintSummary = document.getElementById('btnPrintSummary');
    if (btnPrintSummary) {
        btnPrintSummary.addEventListener('click', (e) => {
            e.preventDefault();
            // Update print time to now
            const printTimeEl = document.getElementById('printCloseTime');
            if (printTimeEl) {
                const now = new Date();
                // Format: HH:MM | DD MMM YYYY (Custom format to match PHP)
                const formatted = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' | ' +
                    now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                printTimeEl.innerText = formatted;
            }

            // 🔹 Sync Dynamic Data for Print
            // FnB
            const fnbKolamVal = document.getElementById('penjualan_fnb_kolam_display')?.value || 'Rp. 0';
            const fnbCafeVal = document.getElementById('penjualan_fnb_cafe_display')?.value || 'Rp. 0';
            if (document.getElementById('printFnbKolam')) document.getElementById('printFnbKolam').innerText = fnbKolamVal;
            if (document.getElementById('printFnbCafe')) document.getElementById('printFnbCafe').innerText = fnbCafeVal;

            // Saldo Akhir
            const saldoAkhirVal = document.getElementById('saldo_akhir_display')?.value || 'Rp. 0';
            if (document.getElementById('printSaldoAkhir')) document.getElementById('printSaldoAkhir').innerText = saldoAkhirVal;

            // Cash In & Cash Out Sums
            // Helper to sum inputs with class .cash-amount
            const sumListRaw = (listId) => {
                const listEl = document.getElementById(listId);
                if (!listEl) return 0;
                return Array.from(listEl.querySelectorAll('.cash-amount')).reduce((acc, el) => acc + (parseInt(el.value || 0)), 0);
            };

            const cashInTotal = sumListRaw('cashInList');
            const cashOutTotal = sumListRaw('cashOutList');

            // Use existing formatRupiah
            if (document.getElementById('printCashIn')) document.getElementById('printCashIn').innerText = formatRupiah(cashInTotal);
            if (document.getElementById('printCashOut')) document.getElementById('printCashOut').innerText = formatRupiah(cashOutTotal);

            window.print();
        });
    }

    // 🔹 Tombol export excel
    const btnExport = document.getElementById('btnExportReport');
    if (btnExport) {
        btnExport.addEventListener('click', () => {
            window.location.href = window.CashierRoutes.exportReport;
        });
    }

    // 🔹 Tombol export pdf
    const btnExportPdf = document.getElementById('btnExportPdf');
    if (btnExportPdf) {
        btnExportPdf.addEventListener('click', () => {
            window.location.href = window.CashierRoutes.exportReport + '?type=pdf';
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

    setupRupiahInput(kolamDisplay, kolamHidden);
    setupRupiahInput(cafeDisplay, cafeHidden);

    const computeFinalBalance = () => {
        const saldoAwal = (window.CashierData && window.CashierData.saldoAwal) ? parseInt(window.CashierData.saldoAwal) : 0;
        const penjualanTiketTunai = (window.CashierData && window.CashierData.purchaseTunai) ? parseInt(window.CashierData.purchaseTunai) : 0;
        const kolam = parseInt(kolamHidden?.value || 0);
        const cafe = parseInt(cafeHidden?.value || 0);
        const sumList = (listEl) => {
            return Array.from(listEl.querySelectorAll('.cash-amount')).reduce((acc, el) => acc + (parseInt(el.value || 0)), 0);
        };
        const cashInSum = cashInList ? sumList(cashInList) : 0;
        const cashOutSum = cashOutList ? sumList(cashOutList) : 0;
        const total = saldoAwal + penjualanTiketTunai + kolam + cafe + cashInSum - cashOutSum;
        const displayEl = document.getElementById('saldo_akhir_display');
        const hiddenEl = document.getElementById('saldo_akhir');
        if (displayEl) displayEl.value = formatRupiah(total);
        if (hiddenEl) hiddenEl.value = total;
    };

    const setupCashRow = (rowEl, onChange) => {
        const amountDisplayEl = rowEl.querySelector('.cash-amount-display');
        const amountHiddenEl = rowEl.querySelector('.cash-amount');
        const removeBtn = rowEl.querySelector('.cash-remove');
        setupRupiahInput(amountDisplayEl, amountHiddenEl);
        amountDisplayEl.addEventListener('input', onChange);
        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                rowEl.remove();
                onChange();
            });
        }
    };

    const createCashInRow = () => {
        const div = document.createElement('div');
        div.className = 'cash-row';
        div.innerHTML = `
            <input type="text" class="cash-amount-display closecashier-input" placeholder="Rp. 0" value="Rp. 0">
            <input type="hidden" class="cash-amount" value="0">
            <input type="text" class="cash-notes closecashier-input" placeholder="Keterangan Cash In">
            <button type="button" class="btn-danger cash-remove">Hapus</button>
        `;
        cashInList.appendChild(div);
        setupCashRow(div, computeFinalBalance);
    };

    const createCashOutRow = () => {
        const div = document.createElement('div');
        div.className = 'cash-row';
        div.innerHTML = `
            <input type="text" class="cash-amount-display closecashier-input" placeholder="Rp. 0" value="Rp. 0">
            <input type="hidden" class="cash-amount" value="0">
            <input type="text" class="cash-notes closecashier-input" placeholder="Keterangan Cash Out">
            <button type="button" class="btn-danger cash-remove">Hapus</button>
        `;
        cashOutList.appendChild(div);
        setupCashRow(div, computeFinalBalance);
    };

    if (btnAddCashInRow) {
        btnAddCashInRow.addEventListener('click', () => {
            createCashInRow();
        });
    }
    if (btnAddCashOutRow) {
        btnAddCashOutRow.addEventListener('click', () => {
            createCashOutRow();
        });
    }

    kolamDisplay && kolamDisplay.addEventListener('input', computeFinalBalance);
    cafeDisplay && cafeDisplay.addEventListener('input', computeFinalBalance);
    computeFinalBalance();


    // 🔹 Tombol tutup kasir & logout
    const btnProcess = document.getElementById('btnProcessClose');
    if (btnProcess) {
        btnProcess.addEventListener('click', async () => {
            const saldoAkhir = toNumber(document.getElementById('saldo_akhir_display').value);
            const penjualanFnbKolam = toNumber(kolamDisplay.value);
            const penjualanFnbCafe = toNumber(cafeDisplay.value);

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
                const rowsToArray = (listEl, type) => {
                    return Array.from(listEl.querySelectorAll('.cash-row')).map(row => {
                        const amount = toNumber(row.querySelector('.cash-amount-display').value);
                        const notes = row.querySelector('.cash-notes').value || '';
                        return {
                            nominal_uang: amount,
                            type: type,
                            keterangan: notes
                        };
                    }).filter(item => item.nominal_uang > 0 && item.keterangan.trim() !== '');
                };
                const cashInArray = cashInList ? rowsToArray(cashInList, 1) : [];
                const cashOutArray = cashOutList ? rowsToArray(cashOutList, 2) : [];
                const cashInOut = [...cashInArray, ...cashOutArray];

                const payload = {
                    saldo_akhir: saldoAkhir,
                    penjualan_fnb_kolam: penjualanFnbKolam,
                    penjualan_fnb_cafe: penjualanFnbCafe,
                    cash_in_out: cashInOut
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
