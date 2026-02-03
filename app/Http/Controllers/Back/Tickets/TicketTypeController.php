<?php

namespace App\Http\Controllers\Back\Tickets;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketTypeController extends Controller
{
    public function index(Request $request) {
        $isActive = $request->input('is_active');
        $query = TicketType::query();

        // Filter status aktif / tidak aktif
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', (bool) $isActive);
        }

        $ticketTypes = $query->where('deleted_at', null)->orderBy('weight', 'asc')->paginate(10);
        return view('back.tickets.index', compact('ticketTypes'));
    }

    public function add(Request $request){
        // dd($request->all());
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|integer',
                'validity_type' => 'required|in:duration,lifetime',

                'duration' => 'required_if:validity_type,duration|nullable|integer|min:1',

                'qty_extra' => 'nullable|integer|min:0',
                'weight' => 'nullable|integer|min:1',
                'is_dob_mandatory' => 'required|integer',
                'is_phone_mandatory' => 'required|integer',
                'is_active' => 'required|integer',
                'can_buy_tiket_pengantar' => 'required|integer',
                'is_coach_club_require' => 'required|integer',
                'tipe_khusus' => 'required|integer',
                'ticket_kode_ref' => 'required|string|max:255'
            ]);

            $ticketType = new TicketType();
            $ticketType->name = $request->name;
            $ticketType->price = $request->price;
            $ticketType->validity_type = $request->validity_type;

            if ($request->validity_type === 'lifetime') {
                $ticketType->duration = null;
            } else {
                $ticketType->duration = $request->duration;
            }

            // Handle Extra Tiket (Default 0 if null/empty)
            $ticketType->qty_extra = $request->input('qty_extra') ?? 0;

            // Handle Weight (Auto-increment if null/empty)
            if ($request->filled('weight')) {
                $ticketType->weight = $request->weight;
            } else {
                $maxWeight = TicketType::max('weight');
                $ticketType->weight = $maxWeight ? $maxWeight + 1 : 1;
            }

            $ticketType->is_dob_mandatory = $request->is_dob_mandatory;
            $ticketType->is_phone_mandatory = $request->is_phone_mandatory;
            $ticketType->is_active = $request->is_active;
            $ticketType->can_buy_tiket_pengantar = $request->can_buy_tiket_pengantar;
            $ticketType->is_coach_club_require = $request->is_coach_club_require;
            $ticketType->tipe_khusus = $request->tipe_khusus;
            $ticketType->ticket_kode_ref = $request->ticket_kode_ref;
            
            if (!$ticketType->save()) {
                throw new \Exception("Gagal menyimpan data Ticket Type.");
            }

            DB::commit();

            return redirect()->route('ticket-types')->with([
                'success' => 'Berhasil menambahkan Ticket Type !',
                'action' => 'add'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $ticketType = TicketType::findOrFail($id);
        $ticketType->delete();

        return redirect()->route('ticket-types')->with([
            'success' => 'Berhasil menghapus Ticket Type !',
            'action' => 'delete'
        ]);
    }

    public function getTicketTypes($id){
        $ticketType = TicketType::findOrFail($id);
        
        return response()->json($ticketType);
    }

    public function edit(Request $request, $id){
        try {
            DB::beginTransaction();

            $ticketType = TicketType::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|integer',
                'validity_type' => 'required|in:duration,lifetime',

                'duration' => 'required_if:validity_type,duration|nullable|integer|min:1',

                'qty_extra' => 'nullable|integer|min:0',
                'weight' => 'nullable|integer|min:1',
                'is_dob_mandatory' => 'required|integer',
                'is_phone_mandatory' => 'required|integer',
                'is_active' => 'required|integer',
                'can_buy_tiket_pengantar' => 'required|integer',
                'is_coach_club_require' => 'required|integer',
                'tipe_khusus' => 'required|integer',
                'ticket_kode_ref' => 'required|string|max:255'
            ]);

            $ticketType->name = $request->name;
            $ticketType->price = $request->price;
            $ticketType->validity_type = $request->validity_type;

            if ($request->validity_type === 'lifetime') {
                $ticketType->duration = null;
            } else {
                $ticketType->duration = $request->duration;
            }

            // Handle Extra Tiket (Default 0 if null/empty)
            $ticketType->qty_extra = $request->input('qty_extra') ?? 0;

            // Handle Weight (Auto-increment if null/empty)
            if ($request->filled('weight')) {
                $ticketType->weight = $request->weight;
            } else {
                $maxWeight = TicketType::max('weight');
                $ticketType->weight = $maxWeight ? $maxWeight + 1 : 1;
            }

            $ticketType->is_dob_mandatory = $request->is_dob_mandatory;
            $ticketType->is_phone_mandatory = $request->is_phone_mandatory;
            $ticketType->is_active = $request->is_active;
            $ticketType->can_buy_tiket_pengantar = $request->can_buy_tiket_pengantar;
            $ticketType->is_coach_club_require = $request->is_coach_club_require;
            $ticketType->tipe_khusus = $request->tipe_khusus;
            $ticketType->ticket_kode_ref = $request->ticket_kode_ref;

            if (!$ticketType->save()) {
                throw new \Exception("Gagal memperbarui data Ticket Type.");
            }

            DB::commit();

            return redirect()->route('ticket-types')->with([
                'success' => 'Berhasil memperbarui Ticket Type !',
                'action' => 'edit'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

}
