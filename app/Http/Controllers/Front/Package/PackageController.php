<?php

namespace App\Http\Controllers\Front\Package;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LogRedeemPacketTicket;
use App\Models\LogQtyPacketTicket;
use App\Models\PackageComboRedeem;
use App\Models\Ticket;
use App\Models\TicketEntry;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function inputPackage()
    {
        return view('front.print_ticket.package.input-package');
    }

    // public function checkPackage(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'phone' => [
    //             'required',
    //             'regex:/^08[1-9][0-9]{6,10}$/'
    //         ],
    //         'qty_redeem' => 'required|integer|min:1',
    //     ], [
    //         'phone.required' => 'Nomor telepon wajib diisi.',
    //         'phone.regex' => 'Format nomor telepon tidak valid. Harus diawali 08 dan panjang 9–12 digit.',
    //         'qty_redeem.required' => 'Jumlah paket yang di-redeem wajib diisi.',
    //         'qty_redeem.integer' => 'Jumlah paket yang di-redeem harus berupa angka.',
    //         'qty_redeem.min' => 'Jumlah paket yang di-redeem minimal 1.',
    //     ]);

    //     $today = Carbon::today();

    //     // 1️⃣ Cari customer
    //     $customer = Customer::where('phone', $validatedData['phone'])
    //         ->whereNull('deleted_at')
    //         ->first();

    //     // Are untuk log redeem 
    //     $logRedeem = new LogRedeemPacketTicket();
    //     $logRedeem->phone = $validatedData['phone'];
    //     $logRedeem->redeem_qty = $validatedData['qty_redeem'];

    //     if ($customer){
    //         $logRedeem->customer_id = $customer->id;
    // 		$logRedeem->customer_name = $customer->name;
    // 		$logRedeem->status = 1;
    // 		$logRedeem->save();
    //     }

    //     if (!$customer) {
    //         throw ValidationException::withMessages([
    //             'phone' => 'Nomor telepon tidak ditemukan.'
    //         ]);
    //     }

    //     // 2️⃣ Ambil package combo redeem yang masih aktif
    //     $packageComboRedeems = PackageComboRedeem::where('customer_id', $customer->id)
    //         ->whereNull('fully_redeemed_at')
    //         ->where('expired_date', '>=', $today)
    //         ->with('details')
    //         ->get();

    //     if ($packageComboRedeems->isEmpty()) {
    //         throw ValidationException::withMessages([
    //             'phone' => 'Tidak ada paket yang dapat di-redeem atau paket sudah kedaluwarsa.'
    //         ]);
    //     }

    //     // 3️⃣ Ambil semua detail valid
    //     $packageDetailIds = $packageComboRedeems->flatMap(fn($p) => $p->details->pluck('id'))->unique()->values();

    //     if ($packageDetailIds->isEmpty()) {
    //         throw ValidationException::withMessages([
    //             'phone' => 'Paket tidak memiliki detail redeem yang valid.'
    //         ]);
    //     }

    //     // 4️⃣ Hitung total sisa redeem yang tersedia
    //     $totalAvailable = $packageComboRedeems->sum(fn($p) => $p->details->sum('qty_redeemed'));

    //     if ($validatedData['qty_redeem'] > $totalAvailable) {
    //         throw ValidationException::withMessages([
    //             'qty_redeem' => 'Jumlah redeem melebihi sisa tiket yang tersedia.'
    //         ]);
    //     }

    //     // 5️⃣ Ambil tiket aktif sesuai jumlah redeem
    //     $tickets = Ticket::where('customer_id', $customer->id)
    //         ->whereIn('package_combo_redeem_detail_id', $packageDetailIds)
    //         ->where('is_active', 1)
    //         ->whereDate('date_start', '<=', $today)
    //         ->whereDate('date_end', '>=', $today)
    //         ->take($validatedData['qty_redeem'])
    //         ->get();

    //     if ($tickets->isEmpty()) {
    //         throw ValidationException::withMessages([
    //             'phone' => 'Tidak ada tiket aktif yang bisa di-redeem.'
    //         ]);
    //     }

    //     // 6️⃣ Update tiket jadi nonaktif
    //     Ticket::whereIn('id', $tickets->pluck('id'))->update(['is_active' => 0]);

    //     // 7️⃣ Update qty_redeemed & qty_printed
    //     $remainingToRedeem = $validatedData['qty_redeem'];

    //     foreach ($packageComboRedeems as $package) {
    //         foreach ($package->details as $detail) {
    //             if ($remainingToRedeem <= 0)
    //                 break;

    //             $available = $detail->qty_redeemed; // sisa yang bisa diredeem
    //             if ($available <= 0)
    //                 continue;

    //             $redeemNow = min($available, $remainingToRedeem);

    //             $detail->qty_redeemed -= $redeemNow;
    //             $detail->qty_printed += $redeemNow;
    //             $detail->save();

    //             $remainingToRedeem -= $redeemNow;
    //         }

    //         // Jika semua detail qty_redeemed == 0, maka fully redeemed
    //         $allRedeemed = $package->details->every(fn($d) => $d->qty_redeemed <= 0);
    //         if ($allRedeemed) {
    //             $package->fully_redeemed_at = now();
    //             $package->save();
    //         }

    //         if ($remainingToRedeem <= 0)
    //             break;
    //     }

    //     // 8️⃣ Ambil ticket entries
    //     $ticketIds = $tickets->pluck('id');
    //     $ticketEntries = TicketEntry::whereIn('ticket_id', $ticketIds)->get();

    //     // 9️⃣ Hitung data view
    //     $redeemCount = $validatedData['qty_redeem'];
    //     $totalRedeemed = $packageComboRedeems->sum(fn($p) => $p->details->sum('qty_printed')); // total sudah di-redeem
    //     $totalRemaining = $packageComboRedeems->sum(fn($p) => $p->details->sum('qty_redeemed')); // total sisa
    //     $expiredDate = $packageComboRedeems->min('expired_date');

    //     return view('front.print_ticket.package.print-ticket-package', [
    //         'customer' => $customer,
    //         'tickets' => $tickets,
    //         'ticketEntries' => $ticketEntries,
    //         'redeemCount' => $redeemCount,
    //         'totalRedeemed' => $totalRedeemed,
    //         'totalPrinted' => $totalRedeemed,
    //         'totalRemaining' => $totalRemaining,
    //         'expiredDate' => $expiredDate,
    //     ]);
    // }

    public function checkPackage(Request $request)
    {
        $validatedData = $request->validate([
            'phone' => [
                'required',
                'regex:/^08[1-9][0-9]{6,10}$/'
            ],
            'qty_redeem' => 'required|integer|min:1',
        ], [
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Format nomor telepon tidak valid. Harus diawali 08 dan panjang 9–12 digit.',
            'qty_redeem.required' => 'Jumlah paket yang di-redeem wajib diisi.',
            'qty_redeem.integer' => 'Jumlah paket yang di-redeem harus berupa angka.',
            'qty_redeem.min' => 'Jumlah paket yang di-redeem minimal 1.',
        ]);

        $today = Carbon::today();

        // 1️⃣ Cari customer
        $customer = Customer::where('phone', $validatedData['phone'])
            ->whereNull('deleted_at')
            ->first();

        // 2️⃣ Catat log redeem utama
        $logRedeem = new LogRedeemPacketTicket();
        $logRedeem->phone = $validatedData['phone'];
        $logRedeem->redeem_qty = $validatedData['qty_redeem'];

        if ($customer) {
            $logRedeem->customer_id = $customer->id;
            $logRedeem->customer_name = $customer->name;
            $logRedeem->status = 1;
            $logRedeem->save(); // disimpan dulu agar dapat ID
        } else {
            throw ValidationException::withMessages([
                'phone' => 'Nomor telepon tidak ditemukan.'
            ]);
        }

        // 3️⃣ Ambil package combo redeem aktif
        $packageComboRedeems = PackageComboRedeem::where('customer_id', $customer->id)
            ->whereNull('fully_redeemed_at')
            ->where('expired_date', '>=', $today)
            ->with('details')
            ->get();

        if ($packageComboRedeems->isEmpty()) {
            throw ValidationException::withMessages([
                'phone' => 'Tidak ada paket yang dapat di-redeem atau paket sudah kedaluwarsa.'
            ]);
        }

        // 4️⃣ Ambil semua detail valid
        $packageDetailIds = $packageComboRedeems->flatMap(fn($p) => $p->details->pluck('id'))->unique()->values();

        if ($packageDetailIds->isEmpty()) {
            throw ValidationException::withMessages([
                'phone' => 'Paket tidak memiliki detail redeem yang valid.'
            ]);
        }

        // 5️⃣ Hitung total sisa redeem
        $totalAvailable = $packageComboRedeems->sum(fn($p) => $p->details->sum('qty_redeemed'));

        if ($validatedData['qty_redeem'] > $totalAvailable) {
            throw ValidationException::withMessages([
                'qty_redeem' => 'Jumlah redeem melebihi sisa tiket yang tersedia.'
            ]);
        }

        // 6️⃣ Ambil tiket aktif sesuai jumlah redeem
        $tickets = Ticket::where('customer_id', $customer->id)
            ->whereIn('package_combo_redeem_detail_id', $packageDetailIds)
            ->where('is_active', 1)
            ->whereDate('date_start', '<=', $today)
            ->whereDate('date_end', '>=', $today)
            ->take($validatedData['qty_redeem'])
            ->get();

        if ($tickets->isEmpty()) {
            throw ValidationException::withMessages([
                'phone' => 'Tidak ada tiket aktif yang bisa di-redeem.'
            ]);
        }

        // 7️⃣ Update tiket jadi nonaktif
        Ticket::whereIn('id', $tickets->pluck('id'))->update(['is_active' => 0]);

        // 8️⃣ Update qty_redeemed & qty_printed
        $remainingToRedeem = $validatedData['qty_redeem'];

        foreach ($packageComboRedeems as $package) {
            foreach ($package->details as $detail) {
                if ($remainingToRedeem <= 0)
                    break;

                $available = $detail->qty_redeemed;
                if ($available <= 0)
                    continue;

                $redeemNow = min($available, $remainingToRedeem);
                $detail->qty_redeemed -= $redeemNow;
                $detail->qty_printed += $redeemNow;
                $detail->save();

                $remainingToRedeem -= $redeemNow;
            }

            // Jika semua detail sudah habis, tandai fully redeemed
            $allRedeemed = $package->details->every(fn($d) => $d->qty_redeemed <= 0);
            if ($allRedeemed) {
                $package->fully_redeemed_at = now();
                $package->save();
            }

            if ($remainingToRedeem <= 0)
                break;
        }

        // 9️⃣ Catat log per tiket (detail)
        foreach ($tickets as $ticket) {
            $logQtyPacket = new LogQtyPacketTicket();
            $logQtyPacket->log_redeem_packet_tickets_id  = $logRedeem->id; // relasi ke log utama
            $logQtyPacket->package_combo_redeem_id = $ticket->packageComboRedeemDetail->package_combo_redeem_id ?? null;
            $logQtyPacket->package_combo_detail_id = $ticket->package_combo_redeem_detail_id ?? null;
            $logQtyPacket->save();
        }

        // 🔟 Ambil ticket entries
        $ticketIds = $tickets->pluck('id');
        $ticketEntries = TicketEntry::whereIn('ticket_id', $ticketIds)->get();

        // 11️⃣ Hitung data view
        $redeemCount = $validatedData['qty_redeem'];
        $totalRedeemed = $packageComboRedeems->sum(fn($p) => $p->details->sum('qty_printed'));
        $totalRemaining = $packageComboRedeems->sum(fn($p) => $p->details->sum('qty_redeemed'));
        $expiredDate = $packageComboRedeems->min('expired_date');

        return view('front.print_ticket.package.print-ticket-package', [
            'customer' => $customer,
            'tickets' => $tickets,
            'ticketEntries' => $ticketEntries,
            'redeemCount' => $redeemCount,
            'totalRedeemed' => $totalRedeemed,
            'totalPrinted' => $totalRedeemed,
            'totalRemaining' => $totalRemaining,
            'expiredDate' => $expiredDate,
        ]);
    }

}
