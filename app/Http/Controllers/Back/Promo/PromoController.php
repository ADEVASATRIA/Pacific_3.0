<?php

namespace App\Http\Controllers\Back\Promo;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use Illuminate\Http\Request;
use App\Models\Promo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PromoController extends Controller
{

    private $validatedFields = [
        'code' => 'required|string|max:255|unique:promos,code',
        'type' => 'required',
        'value' => 'required',
        'is_active' => 'required',
        'description' => 'nullable',
        'expired_date' => 'required|date',
        'start_date' => 'required|date',
        'min_purchase' => 'required|numeric',
        'max_discount' => 'required|numeric',
        'quota' => 'required|numeric',
        'ticket_types' => 'required|array',
    ];

    public function index(Request $request)
    {
        $isActive = $request->input('is_active');
        $name = $request->input('name');

        $query = Promo::query();
        $tickets = TicketType::all();

        // Filter status aktif / tidak aktif
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', (bool) $isActive);
        }

        // Filter nama promo
        if ($name !== null && $name !== '') {
            $query->where('code', 'like', '%' . $name . '%');
        }

        // Ambil data dengan pagination (10 per halaman)
        $promo = $query->whereNull('deleted_at')->orderBy('created_at', 'desc')->paginate(10);

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
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), $this->validatedFields);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            Promo::create($validator->validated());

            DB::commit();

            return redirect()->route('promo')->with([
                'success' => 'Berhasil menambahkan promo!',
                'action' => 'add'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with([
                    'error' => 'Terjadi kesalahan: ' . $th->getMessage(),
                    'action' => 'add'
                ]);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $promo = Promo::findOrFail($id);

            // Adjust unique validation for update
            $rules = $this->validatedFields;
            $rules['code'] = 'required|string|max:255|unique:promos,code,' . $id;

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $promo->update($validator->validated());

            DB::commit();

            return redirect()->route('promo')->with([
                'success' => 'Berhasil mengupdate promo!',
                'action' => 'edit'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with([
                    'error' => 'Terjadi kesalahan: ' . $th->getMessage(),
                    'action' => 'edit'
                ]);
        }
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

    public function detail($id){
        $promo = Promo::findOrFail($id);
        
        // Fetch ticket names based on the JSON array in ticket_types
        $ticketIds = $promo->ticket_types ?? [];
        // Ensure it's an array (handle case where it might be null or not an array)
        if (!is_array($ticketIds)) {
            $ticketIds = [];
        }
        
        $ticketNames = TicketType::whereIn('id', $ticketIds)->pluck('name')->toArray();

        return view('back.partials.promo.promo_detail', compact('promo', 'ticketNames'));
    }
}
