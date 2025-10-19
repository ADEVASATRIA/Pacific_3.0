document.addEventListener("DOMContentLoaded", function () {
    console.log("checkout_view.js loaded ✅");

    // === ELEMENTS ===
    const select = document.querySelector(".custom-select");
    const selected = select?.querySelector(".selected");
    const options = select?.querySelector(".options");
    const hiddenInput = document.getElementById("payment-method");

    const approvalBox = document.getElementById("approval-code-box");
    const moneyBox = document.getElementById("money-box");
    const cardBox = document.getElementById("card-box");

    const form = document.querySelector("form");

    // === PAYMENT METHOD DROPDOWN ===
    if (selected && options && hiddenInput) {
        selected.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            document.querySelectorAll(".custom-select.open").forEach(other => {
                if (other !== select) other.classList.remove("open");
            });

            select.classList.toggle("open");
        });

        options.querySelectorAll("div[data-value]").forEach(option => {
            option.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();

                const value = parseInt(option.dataset.value);
                selected.innerHTML = option.innerHTML;
                hiddenInput.value = value;
                select.classList.remove("open");
                select.classList.add("selected");

                // Tampilkan box sesuai metode
                if (value === 1) {
                    approvalBox.style.display = "none";
                    cardBox.style.display = "none";
                    moneyBox.style.display = "grid";
                } else if ([2, 3, 7, 6].includes(value)) {
                    approvalBox.style.display = "block";
                    cardBox.style.display = "none";
                    moneyBox.style.display = "none";
                } else if ([4, 5, 8].includes(value)) {
                    approvalBox.style.display = "block";
                    cardBox.style.display = "block";
                    moneyBox.style.display = "none";
                }

                console.log("Payment method selected:", value);
            });
        });

        // Tutup dropdown jika klik di luar
        document.addEventListener("click", function (e) {
            if (!select.contains(e.target)) {
                select.classList.remove("open");
            }
        });
    }

    // === UANG DITERIMA & KEMBALIAN ===
    const uangDisplay = document.getElementById("uangDiterima");
    const uangHidden = document.getElementById("uangDiterima_hidden");
    const kembaliDisplay = document.getElementById("kembalian");
    const kembaliHidden = document.getElementById("kembalian_hidden");
    const totalField = document.querySelector('[name="total"]');
    const totalValue = parseInt((totalField?.value || "0").replace(/[^0-9]/g, "")) || 0;

    // Fungsi bantu
    const formatRupiah = n =>
        "Rp. " + n.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    const toNumber = str =>
        parseInt(str.replace(/[^0-9]/g, "")) || 0;

    if (uangDisplay) {
        uangDisplay.addEventListener("input", function () {
            const raw = this.value.replace(/[^0-9]/g, "");
            if (raw === "") {
                this.value = "";
                uangHidden.value = 0;
                kembaliDisplay.value = "";
                kembaliHidden.value = 0;
                return;
            }

            // Format tampilan
            this.value = formatRupiah(raw);

            // Isi hidden angka murni
            uangHidden.value = parseInt(raw);

            // Hitung kembalian
            const received = parseInt(raw || 0);
            const change = Math.max(received - totalValue, 0);

            kembaliDisplay.value = formatRupiah(change.toString());
            kembaliHidden.value = change;
        });
    }

    // === PASTIKAN NILAI BERSIH SAAT SUBMIT ===
    if (form) {
        form.addEventListener("submit", function () {
            uangHidden.value = toNumber(uangDisplay?.value || "0");
            kembaliHidden.value = toNumber(kembaliDisplay?.value || "0");
        });
    }

    // === SLIDER (opsional) ===
    const slides = document.querySelectorAll(".ads-slider img");
    let index = 0;
    if (slides.length > 0) {
        slides[index].classList.add("active");
        setInterval(() => {
            slides[index].classList.remove("active");
            index = (index + 1) % slides.length;
            slides[index].classList.add("active");
        }, 4000);
    }
});
