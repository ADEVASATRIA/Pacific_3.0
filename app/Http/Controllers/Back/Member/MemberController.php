<?php

namespace App\Http\Controllers\Back\Member;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date_format:Y-m-d',
            'awal_masa' => 'nullable|date_format:Y-m-d',
            'akhir_masa' => 'nullable|date_format:Y-m-d|after_or_equal:awal_masa',
            'active' => 'nullable|in:0,1',
        ]);

        $today = now()->startOfDay();

        $members = Customer::query()
            ->whereNotNull('member_id')
            ->whereHas('tiketTerbaru', function ($q) use ($request, $today) {
                if ($request->status === 'active') {
                    $q->whereDate('date_end', '>=', $today)
                        ->where('code', 'LIKE', 'M%');
                }

                if ($request->status === 'all') {
                    $q->where('code', 'LIKE', 'M%');
                }

                if ($request->awal_masa) {
                    $q->whereDate('date_start', '>=', Carbon::parse($request->awal_masa));
                }
                if ($request->akhir_masa) {
                    $q->whereDate('date_end', '<=', Carbon::parse($request->akhir_masa));
                }

                if ($request->status === 'expired') {
                    $q->whereDate('date_end', '<', $today)
                        ->where('code', 'LIKE', 'M%');
                }
            })
            ->when($request->name, fn($q, $name) => $q->where('name', 'LIKE', "%$name%"))
            ->when($request->dob, fn($q, $dob) => $q->whereDate('dob', Carbon::parse($dob)->startOfDay()))
            ->when($request->phone, fn($q, $phone) => $q->where('phone', $phone))
            ->with([
                'tiketTerbaru',
                'tickets' => fn($q) => $q->where('code', 'LIKE', 'M%')
                    ->orderBy('created_at', 'desc'),
            ])
            ->paginate(10);

        return view('back.member.index', compact('members'));
    }

    public function getMember($id)
    {
        $member = Customer::with('tickets')->find($id);

        // Ambil tiket terbaru
        $ticket = Ticket::where('customer_id', $member->id)
            ->where('ticket_kode_ref', 'like', 'M%')
            ->latest('created_at')
            ->first();

        // Kembalikan data member dan tiket terbaru
        return response()->json([
            'member' => $member,
            'ticket' => $ticket
        ]);
    }

    public function edit(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'awal_masa_berlaku' => 'nullable|date',
            'akhir_masa_berlaku' => 'nullable|date',
        ]);

        $member = Customer::findOrFail($id);

        // Update data member dasar
        $member->name = $request->name;
        $member->phone = $request->phone;
        $member->dob = $request->dob;
        $member->save();

        // Ambil tiket terbaru berdasarkan created_at DESC
        $ticket = Ticket::where('customer_id', $member->id)
            ->where('ticket_kode_ref', 'like', 'M%')
            ->latest('created_at')
            ->first();

        if ($ticket) {
            $today = now()->startOfDay();
            $end = $request->akhir_masa_berlaku
                ? Carbon::parse($request->akhir_masa_berlaku)->startOfDay()
                : null;

            // Update tanggal start & end
            $ticket->date_start = $request->awal_masa_berlaku;
            $ticket->date_end = $request->akhir_masa_berlaku;

            // Validasi tambahan: set is_active sesuai date_end
            if ($end && $end->gte($today)) {
                $ticket->is_active = true;   // masih berlaku
            } else {
                $ticket->is_active = false;  // expired otomatis
            }

            $ticket->save();

            // Sinkronkan juga ke customer
            $member->awal_masa_berlaku = $request->awal_masa_berlaku;
            $member->akhir_masa_berlaku = $request->akhir_masa_berlaku;
            $member->save();
        }

        return redirect()->route('member')->with([
            'success' => true,
            'action' => 'edit'
        ]);
    }

    public function delete($id)
    {
        $member = Customer::find($id);
        $member->awal_masa_berlaku = null;
        $member->akhir_masa_berlaku = null;
        $member->save();

        Ticket::where('customer_id', $member->id)
            ->where('is_active', true)
            ->where('ticket_kode_ref', 'like', 'M%')
            ->update([
                'is_active' => false,
            ]);

        return redirect()->route('member')->with([
            'success' => true,
            'action' => 'delete'
        ]);
    }
}
