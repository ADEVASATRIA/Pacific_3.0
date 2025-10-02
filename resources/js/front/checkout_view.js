document.addEventListener("DOMContentLoaded", function () {
    console.log("checkout_view.js loaded ✅");

    const select = document.querySelector(".custom-select");
    const selected = select?.querySelector(".selected");
    const options = select?.querySelector(".options");
    const hiddenInput = document.getElementById("payment-method");

    const approvalBox = document.getElementById("approval-code-box");
    const moneyBox = document.getElementById("money-box");
    const cardBox = document.getElementById("card-box");

    if (!select || !selected || !options || !hiddenInput) {
        console.error("Required elements not found!");
        return;
    }

    // Toggle dropdown
    selected.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        document.querySelectorAll(".custom-select.open").forEach(otherSelect => {
            if (otherSelect !== select) otherSelect.classList.remove("open");
        });
        select.classList.toggle("open");
    });

    // Option selection
    options.querySelectorAll("div[data-value]").forEach(option => {
        option.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            const value = parseInt(option.dataset.value);
            selected.innerHTML = option.innerHTML;
            hiddenInput.value = value;
            select.classList.remove("open");
            select.classList.add("selected");

            // 🔑 Handle visibility
            if (value === 1) { // CASH
                approvalBox.style.display = "none";
                cardBox.style.display = "none";
                moneyBox.style.display = "grid";
            
            } else if ([2, 3, 7, 6].includes(value)) { 
                // 2 = QRIS BCA, 3 = QRIS Mandiri, 7 = QRIS BRI, 6 = Transfer Bank
                approvalBox.style.display = "block";
                cardBox.style.display = "none";
                moneyBox.style.display = "none";
            
            } else if ([4, 5, 8].includes(value)) { 
                // 4 = Debit BCA, 5 = Debit Mandiri, 8 = Debit BRI
                approvalBox.style.display = "block";
                cardBox.style.display = "block";
                moneyBox.style.display = "none";
            }


            console.log("Payment method selected:", value);
        });
    });

    // Close dropdown on outside click
    document.addEventListener("click", function (e) {
        if (!select.contains(e.target)) {
            select.classList.remove("open");
        }
    });

    // Auto hitung kembalian
    const uangDiterimaInput = document.querySelector('[name="uangDiterima"]');
    const kembalianInput = document.querySelector('[name="kembalian"]');
    if (uangDiterimaInput && kembalianInput) {
        uangDiterimaInput.addEventListener("input", e => {
            const total = parseInt(document.querySelector('[name="total"]').value || 0);
            const received = parseInt(e.target.value || 0);
            kembalianInput.value = Math.max(received - total, 0);
        });
    }

    const slides = document.querySelectorAll(".ads-slider img");
        let index = 0;

        if (slides.length > 0) {
            slides[index].classList.add("active");

            setInterval(() => {
                slides[index].classList.remove("active");
                index = (index + 1) % slides.length;
                slides[index].classList.add("active");
            }, 4000); // ganti gambar tiap 4 detik
        }
});