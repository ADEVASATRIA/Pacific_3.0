import 'bootstrap/dist/js/bootstrap.bundle.min.js';

document.addEventListener('DOMContentLoaded', () => {
    const filterForm = document.getElementById('filterForm');
    const contentArea = document.getElementById('contentArea');

    if (!filterForm || !contentArea) return;

    filterForm.addEventListener('submit', async function (e) {
        e.preventDefault(); // cegah reload halaman penuh

        // Ambil semua data form dan ubah jadi query string
        const formData = new FormData(this);
        const queryString = new URLSearchParams(formData).toString();

        try {
            // Fetch data ke /transaction (pastikan route-nya benar)
            const response = await fetch(`/transaction?${queryString}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const html = await response.text();

            // Update hanya konten utama (section @yield('content'))
            contentArea.innerHTML = html;

            // Optional: ubah URL di address bar tanpa reload
            window.history.pushState({}, '', `/transaction?${queryString}`);

        } catch (error) {
            console.error('Gagal memuat data filter:', error);
            contentArea.innerHTML = `
                <div class="p-4 text-center text-red-600">
                    Terjadi kesalahan saat memuat data.
                </div>`;
        }
    });
});
