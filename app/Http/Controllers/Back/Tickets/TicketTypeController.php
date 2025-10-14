<?php

namespace App\Http\Controllers\Back\Tickets;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use Illuminate\Http\Request;

class TicketTypeController extends Controller
{
    public function index(Request $request) {
        $isActive = $request->input('is_active');
        $query = TicketType::query();

        // Filter status aktif / tidak aktif
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', (bool) $isActive);
        }

        $ticketTypes = $query->orderBy('weight', 'asc')->paginate(10);
        return view('back.tickets.index', compact('ticketTypes'));
    }

    public function add(Request $request){
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer',
            'duration' => 'required|integer|min:1',
            'qty_extra' => 'required|integer|min:0',
            'weight' => 'required|integer|min:1',
            'is_dob_mandatory' => 'required|integer',
            'is_phone_mandatory' => 'required|integer',
            'is_active' => 'required|integer',
            'can_buy_tiket_pengantar' => 'required|integer',
            'tipe_khusus' => 'required|integer',
            'ticket_kode_ref' => 'required|string|max:255'
        ]);


        $ticketType = new TicketType();
        $ticketType->name = $request->name;
        $ticketType->price = $request->price;
        $ticketType->duration = $request->duration;
        $ticketType->qty_extra = $request->qty_extra;
        $ticketType->weight = $request->weight;
        $ticketType->is_dob_mandatory = $request->is_dob_mandatory;
        $ticketType->is_phone_mandatory = $request->is_phone_mandatory;
        $ticketType->is_active = $request->is_active;
        $ticketType->can_buy_tiket_pengantar = $request->can_buy_tiket_pengantar;
        $ticketType->tipe_khusus = $request->tipe_khusus;
        $ticketType->ticket_kode_ref = $request->ticket_kode_ref;
        $ticketType->save();

        return redirect()->route('ticket-types')->with([
            'success' => true,
            'action' => 'add'
        ]);
    }

    public function delete($id)
    {
        $ticketType = TicketType::findOrFail($id);
        $ticketType->delete();

        return redirect()->route('ticket-types')->with([
            'success' => true,
            'action' => 'delete'
        ]);
    }

    public function getTicketTypes($id){
        $ticketType = TicketType::findOrFail($id);
        
        return response()->json($ticketType);
    }

    public function edit(Request $request, $id){
        $ticketType = TicketType::findOrFail($id);

        $validatedFields = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer',
            'duration' => 'required|integer|min:1',
            'qty_extra' => 'required|integer|min:0',
            'weight' => 'required|integer|min:1',
            'is_dob_mandatory' => 'required|integer',
            'is_phone_mandatory' => 'required|integer',
            'is_active' => 'required|integer',
            'can_buy_tiket_pengantar' => 'required|integer',
            'tipe_khusus' => 'required|integer',
            'ticket_kode_ref' => 'required|string|max:255'
        ]);

        $ticketType->update($validatedFields);

        return redirect()->route('ticket-types')->with([
            'success' => true,
            'action' => 'edit'
        ]);
    }

}
