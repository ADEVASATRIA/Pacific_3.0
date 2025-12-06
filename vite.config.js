import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // === Core Entries ===
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/login.css',

                // === Admin/Back Office Assets ===
                'resources/css/admin/admin.css',
                'resources/css/admin/close-modal.css',
                'resources/css/admin/transaction.css',
                'resources/css/admin/member.css',
                'resources/css/admin/package.css',
                'resources/js/admin/index.js',
                'resources/css/admin/viewHistoryTickets.css',
                
                // === Old Back Office Assets (from previous list) ===
                'resources/css/back/back_blank.css',
                'resources/css/back/partial.css',
                'resources/js/back/transaction-filter.js',

                // === Front Office Assets ===
                'resources/css/front/checkout_finish.css',
                'resources/css/front/checkout_view.css',
                'resources/css/front/index_ticket.css',
                'resources/css/front/beli_ticket.css',
                'resources/css/front/registrasi_customer.css',
                'resources/css/front/print_ticket.css',
                'resources/css/front/input-package.css',
                'resources/css/front/print-ticket-coach.css',
                'resources/css/front/print-ticket-member.css',
                'resources/css/front/print-ticket-package.css',

                // JS QZ Tray Print
                'resources/js/qz-print.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
