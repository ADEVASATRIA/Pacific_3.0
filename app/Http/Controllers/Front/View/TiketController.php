<?php

namespace App\Http\Controllers\Front\View;

use App\Http\Controllers\Controller;
use App\Models\PackageCombo;
use Illuminate\Http\Request;
use App\Models\TicketType;
use App\Models\Customer;

class TiketController extends Controller
{
    public function indexViewTicket(Request $request)
    {
        $customerId = $request->query('customer', null);
        $filterType = $request->query('filter_type', null);

        /*
        |----------------------------------------------------------------------
        | FIX #1 — Gabungkan 5 query TicketType menjadi 1 query tunggal.
        | Sebelumnya: 5× round-trip DB (tipe_khusus 1–5 masing-masing query).
        | Sesudah   : 1× query, hasil di-group per tipe_khusus di memory PHP.
        |
        | Jika filter_type diisi, Eloquent hanya mengambil tipe yang diminta.
        | Filter deleted_at ditangani otomatis oleh SoftDeletes pada model.
        |----------------------------------------------------------------------
        */
        $allTicketTypes = TicketType::select(
                'id', 'name', 'price', 'tipe_khusus', 'weight', 
                'ticket_kode_ref','qty_extra', 'validity_type', 'duration'
            )
            ->where('is_active', 1)
            ->when(
                $filterType && in_array((int) $filterType, [1, 2, 3, 4, 5]),
                fn ($q) => $q->where('tipe_khusus', (int) $filterType)
            )
            ->orderBy('weight', 'asc')
            ->get()
            ->groupBy('tipe_khusus');

        $ticketRegular      = $allTicketTypes->get(1, collect());
        $ticketPengantar    = $allTicketTypes->get(2, collect());
        $ticketPelatih      = $allTicketTypes->get(3, collect());
        $ticketMember       = $allTicketTypes->get(4, collect());
        $ticketBiayaPelatih = $allTicketTypes->get(5, collect());

        /*
        |----------------------------------------------------------------------
        | PackageCombo tetap query terpisah (model berbeda).
        | Filter deleted_at otomatis via SoftDeletes.
        | FIX: tambah select() spesifik — sebelumnya SELECT *.
        |----------------------------------------------------------------------
        */
        $ticketPackage = ($filterType == 6 || $filterType === null)
            ? PackageCombo::select('id', 'name', 'price', 'expired_duration', 'start_date', 'end_date', 'weight')
                ->where('is_active', 1)
                ->orderBy('weight', 'asc')
                ->get()
            : collect();

        /*
        |----------------------------------------------------------------------
        | FIX #3 — Customer: select kolom spesifik (id, name, phone).
        | Sebelumnya: Customer::find() → SELECT * (menarik semua kolom).
        | Blade hanya menggunakan name & phone.
        |----------------------------------------------------------------------
        */
        $customer = $customerId
            ? Customer::select('id', 'name', 'phone')->find($customerId)
            : null;

        return view('front.buy_ticket.ticket_view', compact(
            'ticketRegular',
            'ticketPengantar',
            'ticketPelatih',
            'ticketMember',
            'ticketBiayaPelatih',
            'ticketPackage',
            'customer',
            'customerId',
            'filterType'
        ));
    }
}
