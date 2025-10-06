document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('closeCashierModal');
    const saldoInput = document.getElementById('saldo_akhir');

    document.getElementById('btnOpenCloseModal').onclick = (e) => {
        e.preventDefault();
        modal.style.display = 'flex';
        document.getElementById('closeCashierTime').innerText = new Date().toLocaleString('id-ID');
    };

    document.getElementById('btnCloseModal').onclick = () => {
        modal.style.display = 'none';
    };

    document.getElementById('btnExportReport').onclick = () => {
        window.location.href = "{{ route('cashsession.export') }}";
    };

    document.getElementById('btnProcessClose').onclick = async () => {
        const saldoAkhir = saldoInput.value;
        if (!saldoAkhir) {
            alert('Harap isi saldo akhir terlebih dahulu.');
            return;
        }

        const response = await fetch("{{ route('cashsession.processClose') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ saldo_akhir: saldoAkhir })
        });

        const data = await response.json();
        if (data.success) {
            alert('Kasir berhasil ditutup. Anda akan logout.');
            window.location.href = data.redirect;
        } else {
            alert('Terjadi kesalahan saat menutup kasir.');
        }
    };
});

