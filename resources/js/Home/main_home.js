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

    // 🔹 Fungsi buka modal PIN (global)
    window.handleAdminLogin = function () {
        const modal = document.getElementById('adminPinModal');
        const input = document.getElementById('adminPinInput');
        const errorElement = document.getElementById('pin-error');

        if (modal) modal.style.display = 'flex';
        if (input) {
            input.value = '';
            input.focus();
        }
        if (errorElement) errorElement.style.display = 'none';
    };

    // 🔹 Fungsi tutup modal (global, supaya bisa dipanggil onclick HTML)
    window.closeAdminModal = function () {
        const modal = document.getElementById('adminPinModal');
        if (modal) modal.style.display = 'none';
    };

    // 🔹 Fungsi submit PIN (private)
    function submitAdminPin() {
        const inputPin = document.getElementById('adminPinInput').value;
        const errorElement = document.getElementById('pin-error');

        fetch('/admin/check-pin', {   // ✅ sesuaikan dengan route
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
                window.location.href = data.redirect;
            } else {
                errorElement.innerText = data.message;
                errorElement.style.display = 'block';
                document.getElementById('adminPinInput').value = '';
            }
        })
        .catch(err => {
            errorElement.innerText = 'Terjadi kesalahan. Coba lagi.';
            errorElement.style.display = 'block';
        });
    }

    // 🔹 Bind tombol submit klik
    const submitBtn = document.getElementById('adminPinSubmitBtn');
    if (submitBtn) {
        submitBtn.addEventListener('click', submitAdminPin);
    }

    // 🔹 Support Enter key
    const input = document.getElementById('adminPinInput');
    if (input) {
        input.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') submitAdminPin();
        });
    }
});




