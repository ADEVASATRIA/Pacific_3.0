<?php

namespace App\Http\Controllers\Back\Promo;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use Illuminate\Http\Request;
use App\Models\Promo;
class PromoController extends Controller
{

    private $validatedFields = [
        'code' => 'required',
        'type' => 'required',
        'value' => 'required',
        'is_active' => 'required',
        'description' => 'nullable',
        'expired_date' => 'nullable',
        'start_date' => 'nullable',
        'min_purchase' => 'nullable',
        'max_discount' => 'nullable',
        'quota' => 'nullable|numeric',
        'ticket_types' => 'required|array',
    ];

    public function index(Request $request)
    {
        $isActive = $request->input('is_active');

        $query = Promo::query();
        $tickets = TicketType::all();

        // Filter status aktif / tidak aktif
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', (bool) $isActive);
        }

        // Ambil data dengan pagination (10 per halaman)
        $promo = $query->orderBy('created_at', 'desc')->paginate(10);

        // Kirim variabel ke view
        return view('back.promo.index', compact('promo', 'isActive', 'tickets'));
    }
    public function getPromo($id)
    {
        $promo = Promo::findOrFail($id);
        return response()->json($promo);
    }


    public function add(Request $request)
    {
        $validatedFields = $request->validate($this->validatedFields);

        Promo::create($validatedFields);

        return redirect()->route('promo')->with([
            'success' => true,
            'action' => 'add'
        ]);
    }

    public function edit(Request $request, $id)
    {
        $promo = Promo::findOrFail($id);

        $validatedFields = $request->validate($this->validatedFields);

        $promo->update($validatedFields);

        return redirect()->route('promo')->with([
            'success' => true,
            'action' => 'edit'
        ]);
    }


    public function delete($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->delete();

        return redirect()->route('promo')->with([
            'success' => true,
            'action' => 'delete'
        ]);
    }
}
