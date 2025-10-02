if (!window.__ticketViewInitialized) {
  window.__ticketViewInitialized = true;

  document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.ticket-card') || document;

    container.addEventListener('click', (e) => {
      const btn = e.target.closest('.btn-plus, .btn-minus');
      if (!btn) return;

      e.preventDefault();
      e.stopPropagation();

      const targetId = btn.dataset.target;
      const input = document.getElementById(targetId);
      if (!input) return;

      let value = parseInt(input.value, 10);
      if (isNaN(value)) value = 0;

      if (btn.classList.contains('btn-plus')) {
        input.value = value + 1;
      } else if (btn.classList.contains('btn-minus')) {
        input.value = Math.max(0, value - 1);
      }

      input.dispatchEvent(new Event('input', { bubbles: true }));
    });

    document.querySelectorAll('.qty-input').forEach(input => {
      input.addEventListener('input', function () {
        if (this.value === '' || isNaN(this.value) || parseInt(this.value, 10) < 0) {
          this.value = 0;
        }
      });
      input.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
          e.preventDefault();
        }
      });
    });

    const ticketForm = document.querySelector('.ticket-form');
    const submitBtn = document.getElementById('ticketSubmit');
    const modal = document.getElementById('confirmModal');
    const itemsList = document.getElementById('confirmItems');
    const totalEl = document.getElementById('confirmTotal');
    const dateEl = document.getElementById('confirmDate');
    const btnCancel = document.getElementById('btnCancel');
    const btnConfirm = document.getElementById('btnConfirm');

    if (ticketForm && submitBtn) {
      submitBtn.addEventListener('click', function (e) {
        e.preventDefault();

        itemsList.innerHTML = '';
        let grandTotal = 0;

        document.querySelectorAll('.qty-input').forEach(input => {
          const qty = parseInt(input.value, 10);
          if (qty > 0) {
            const hiddenName = input.name.replace(/\[qty\]/, '[name]');
            const hiddenPrice = input.name.replace(/\[qty\]/, '[price]');
            const ticketName = document.querySelector(`input[name="${hiddenName}"]`)?.value || '';
            const ticketPrice = parseInt(document.querySelector(`input[name="${hiddenPrice}"]`)?.value || 0);

            const subtotal = qty * ticketPrice;
            grandTotal += subtotal;

            const li = document.createElement('li');
            li.textContent = `${ticketName} x ${qty} (Rp ${subtotal.toLocaleString('id-ID')})`;
            itemsList.appendChild(li);
          }
        });

        // tampilkan tanggal & jam sekarang
        const now = new Date();
        dateEl.textContent = now.toLocaleString('id-ID', {
          dateStyle: 'full',
          timeStyle: 'short'
        });

        totalEl.textContent = grandTotal.toLocaleString('id-ID');
        modal.classList.remove('hidden');
      });

      btnCancel.addEventListener('click', () => {
        modal.classList.add('hidden');
      });

      btnConfirm.addEventListener('click', () => {
        modal.classList.add('hidden');
        ticketForm.submit();
      });
    }
  });
}