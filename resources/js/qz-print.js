// resources/js/qz-print.js

// Tunggu event 'qz-loaded' (dipicu saat script qz-tray lokal selesai load)
function waitForQzLoaded(timeout = 10000) {
    return new Promise((resolve, reject) => {
        if (window.qz) return resolve(window.qz);

        let settled = false;
        const onLoaded = () => {
            if (settled) return;
            settled = true;
            clearTimeout(timer);
            resolve(window.qz);
        };

        window.addEventListener('qz-loaded', onLoaded, { once: true });

        const timer = setTimeout(() => {
            if (settled) return;
            settled = true;
            window.removeEventListener('qz-loaded', onLoaded);
            reject(new Error('QZ Tray library not loaded within timeout.'));
        }, timeout);
    });
}

async function initQzSecurity() {
    try {
        await waitForQzLoaded();
        if (!qz || !qz.security) {
            throw new Error('QZ object not found after load.');
        }
        if (!qz.security._hasCustomPromise) {
            qz.security.setCertificatePromise(() => Promise.resolve());
            qz.security.setSignaturePromise((toSign) => Promise.resolve());
            qz.security._hasCustomPromise = true;
        }
    } catch (e) {
        console.warn('initQzSecurity warning:', e);
        // jangan throw supaya UI tetap bisa fallback (browser print)
    }
}

async function _printWithAutoCut() {
    try {
        await initQzSecurity();
        await waitForQzLoaded();

        if (!qz.websocket.isActive()) {
            await qz.websocket.connect();
        }

        const printerName = "Printer POS 58mm"; // pastikan persis
        const config = qz.configs.create(printerName, { size: { width: 58, height: null }});

        const printArea = document.getElementById("print_area");
        if (!printArea) throw new Error("Element #print_area tidak ditemukan.");

        const html = printArea.innerHTML;

        const data = [
            { type: 'raw', format: 'hex', data: '1B40' }, // ESC @
            { type: 'html', data: html },
            { type: 'raw', format: 'hex', data: '1B6D' }  // ESC m (partial cut)
        ];

        await qz.print(config, data);
    } catch (err) {
        // lempar ulang supaya bisa ditangani di wrapper
        throw err;
    }
}

window.printWithAutoCut = function () {
    _printWithAutoCut().catch(err => {
        console.error('Print error:', err);
        alert('Gagal print via QZ Tray: ' + (err && err.message ? err.message : err));
    });
};

document.addEventListener('DOMContentLoaded', () => {
    // opsional: inisialisasi ringan
    initQzSecurity();
});
