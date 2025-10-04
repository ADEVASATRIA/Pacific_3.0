document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.service-card');
    cards.forEach(card => {
        card.style.transition = 'transform 0.15s ease';

        card.addEventListener('click', function () {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });

    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('.parallax-bg');
        if (parallax) {
            parallax.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });
});

window.handleBuyTicket = function () {
    console.log('Buy Ticket clicked');
    window.location.href = '/input-telephone';
}

window.handlePrintPackage = function () {
    console.log('Print Package clicked');
    window.location.href = '/input-package';
}

window.handlePrintMemberTicket = function () {
    console.log('Print Member Ticket clicked');
    window.location.href = '/input-member';
}

window.handlePrintTrainerTicket = function () {
    console.log('Print Trainer Ticket clicked');
    window.location.href = '/input-coach';
}
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('adminPinModal');
    const input = document.getElementById('adminPinInput');
    const errorElement = document.getElementById('pin-error');
    const submitBtn = document.getElementById('adminPinSubmitBtn');

    // 🔹 Fungsi buka modal PIN (global)
    window.handleAdminLogin = function () {
        if (!modal) return;

        modal.classList.remove('hidden');
        input.value = '';
        input.focus();
        errorElement.style.display = 'none';
    };

    // 🔹 Fungsi tutup modal (global)
    window.closeAdminModal = function () {
        if (!modal) return;
        modal.classList.add('hidden');
    };

    // 🔹 Fungsi submit PIN
    window.submitPin = function () {
        const inputPin = input.value.trim();
        if (!inputPin) {
            errorElement.innerText = 'Masukkan PIN terlebih dahulu.';
            errorElement.style.display = 'block';
            return;
        }

        fetch('/admin/check-pin', {   // ✅ sesuaikan route backend kamu
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ pin: inputPin })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.closeAdminModal();
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            } else {
                errorElement.innerText = data.message || 'PIN salah. Silakan coba lagi.';
                errorElement.style.display = 'block';
                input.value = '';
                input.focus();
            }
        })
        .catch(() => {
            errorElement.innerText = 'Terjadi kesalahan. Silakan coba lagi.';
            errorElement.style.display = 'block';
        });
    };

    // 🔹 Bind tombol submit klik
    if (submitBtn) {
        submitBtn.addEventListener('click', window.submitPin);
    }

    // 🔹 Support Enter key
    if (input) {
        input.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') window.submitPin();
        });
    }

    // 🔹 Tutup modal jika klik di luar area konten
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                window.closeAdminModal();
            }
        });
    }
});





