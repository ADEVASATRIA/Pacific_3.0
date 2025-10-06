<?php

namespace App\Http\Controllers\Front\Coach;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\TicketType;
use Illuminate\Http\Request;
use App\Services\Tickets\CreateTickets;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CoachController extends Controller
{
    public function inputCoach()
    {
        return view('front.print_ticket.coach.input-coach');
    }

    public function checkCoach(Request $request, CreateTickets $createTickets)
    {
        $staff = Auth::guard('fo')->user();

        $validatedData = $request->validate([
            'phone' => [
                'required',
                'regex:/^08[1-9][0-9]{6,10}$/'
            ],
        ], [
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Format nomor telepon tidak valid. Harus diawali 08 dan panjang 9–12 digit.',
        ]);

        $today = now()->startOfDay();

        // Cari coach yang masih aktif
        $query = Customer::where('phone', $validatedData['phone'])
            ->whereDate('awal_masa_berlaku', '<=', $today)
            ->whereDate('akhir_masa_berlaku', '>=', $today)
            ->where('is_pelatih', 1);

        // Opsional filter kategori
        if (!empty($validatedData['kategory_customer'])) {
            $query->where('kategory_customer', $validatedData['kategory_customer']);
        }

        $coach = $query->first();

        if (!$coach) {
            return redirect()->back()->withErrors(['phone' => 'Nomor telepon tidak ditemukan atau masa berlaku telah habis, atau bukan pelatih.']);
        }

        $ticketPelatih = TicketType::where('tipe_khusus', 3)->first();
        if (!$ticketPelatih) {
            return redirect()->back()->withErrors(['ticket' => 'Tiket pelatih tidak ditemukan.']);
        }

        DB::beginTransaction();
        try {
            // Buat purchase
            $invoice = 'INV-' . strtoupper(Str::random(6)) . '-' . time();

            $purchase = new Purchase();
            $purchase->customer_id = $coach->id;
            $purchase->staff_id = $staff->id;
            $purchase->invoice_no = $invoice;
            $purchase->sub_total = 0;
            $purchase->tax = 0;
            $purchase->total = $ticketPelatih->price;
            $purchase->status = 2;
            $purchase->payment = 1;
            $purchase->created_at = now();
            $purchase->save();

            // Buat purchase detail
            $purchaseDetail = new PurchaseDetail();
            $purchaseDetail->purchase_id = $purchase->id;
            $purchaseDetail->type = 1;
            $purchaseDetail->purchase_item_id = $ticketPelatih->id;
            $purchaseDetail->name = 'Tiket ' . $ticketPelatih->name;
            $purchaseDetail->price = $ticketPelatih->price;
            $purchaseDetail->qty_extra = $ticketPelatih->qty_extra;
            $purchaseDetail->qty = 1;
            $purchaseDetail->ticket_kode_ref = $ticketPelatih->ticket_kode_ref;
            $purchase->purchaseDetails()->save($purchaseDetail);

            // ✅ Buat tiket & ticket entries pakai service
            $createdTickets = $createTickets->createTicketPelatih($purchaseDetail);

            DB::commit();

            // Return ke blade print ticket coach
            return view('front.print_ticket.coach.print-ticket-coach', [
                'customer' => $coach,
                'tickets' => $createdTickets,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
