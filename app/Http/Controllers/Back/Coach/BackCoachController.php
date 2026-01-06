<?php

namespace App\Http\Controllers\Back\Coach;

use App\Http\Controllers\Controller;
use App\Models\Clubhouse;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackCoachController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search_name' => 'nullable|string|max:10',
        ]);

        // Query mencari nama coach
        $pelatih = Customer::query()
            ->where('is_pelatih', "1")
            ->when($request->search_name, fn($q, $name) => $q->where('name', 'LIKE', "%$name%"))
            ->when($request->status, function ($q, $status) {
                $now = now()->toDateString();
                if ($status == 1) { // Aktif
                    $q->whereDate('awal_masa_berlaku', '<=', $now)
                        ->whereDate('akhir_masa_berlaku', '>=', $now);
                } elseif ($status == 2) { // Tidak Aktif
                    $q->where(function ($sub) use ($now) {
                        $sub->whereDate('awal_masa_berlaku', '>', $now)
                            ->orWhereDate('akhir_masa_berlaku', '<', $now);
                    });
                }
            })
            ->orderBy('name', 'asc')
            ->paginate(10);

        $coaches = $pelatih;
        $clubhouse = Clubhouse::orderBy('name', 'asc')->get();
        return view('back.coach.index', compact('coaches', 'clubhouse'));
    }

    public function add(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'phone' => 'required',
                'awal_masa_berlaku' => 'required|date',
                'akhir_masa_berlaku' => 'required|date',
                'clubhouse_id' => 'required|integer',
            ],
            [
                'name.required' => 'Nama pelatih harus diisi!',
                'phone.required' => 'Nomor Telepon pelatih harus diisi!',
                'awal_masa_berlaku.required' => 'Awal Masa Berlaku pelatih harus diisi!',
                'akhir_masa_berlaku.required' => 'Akhir Masa Berlaku pelatih harus diisi!',
                'clubhouse_id.required' => 'Clubhouse harus dipilih!',
            ]
        );

        if ($request->awal_masa_berlaku > $request->akhir_masa_berlaku) {
            return back()->withErrors("Tanggal Awal Masa Berlaku harus sebelum / sama dengan Akhir Masa Berlaku!");
        } else if ($request->akhir_masa_berlaku < $request->awal_masa_berlaku) {
            return back()->withErrors("Tanggal Akhir Masa Berlaku harus sesudah / sama dengan Awal Masa Berlaku!");
        }

        try {
            DB::beginTransaction();


            $pelatih = Customer::find($request->id);

            if (empty($pelatih))
                $pelatih = new Customer();

            $pelatih->name = $request->name;
            $pelatih->phone = $request->phone;
            $pelatih->awal_masa_berlaku = $request->awal_masa_berlaku;
            $pelatih->akhir_masa_berlaku = $request->akhir_masa_berlaku;
            $pelatih->clubhouse_id = $request->clubhouse_id;
            $pelatih->id_club_renang = $request->clubhouse_id;
            $pelatih->is_pelatih = true;
            $pelatih->save();

            DB::commit();

            return redirect()->route('coach')->with([
                'success' => true,
                'action' => 'add'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Database error: ' . $e->getMessage()])->withInput();
        }
    }

    public function getCoach($id)
    {
        $coach = Customer::find($id);
        $clubhouse = Clubhouse::orderBy('name', 'asc')->get();
        return response()->json([
            'coach' => $coach,
            'clubhouse' => $clubhouse
        ]);
    }

    public function edit(Request $request, $id)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'phone' => 'required',
                'awal_masa_berlaku' => 'required|date',
                'akhir_masa_berlaku' => 'required|date',
                'clubhouse_id' => 'required|integer',
            ],
            [
                'name.required' => 'Nama pelatih harus diisi!',
                'phone.required' => 'Nomor Telepon pelatih harus diisi!',
                'awal_masa_berlaku.required' => 'Awal Masa Berlaku pelatih harus diisi!',
                'akhir_masa_berlaku.required' => 'Akhir Masa Berlaku pelatih harus diisi!',
                'clubhouse_id.required' => 'Clubhouse harus dipilih!',
            ]
        );

        if ($request->awal_masa_berlaku > $request->akhir_masa_berlaku) {
            return back()->withErrors("Tanggal Awal Masa Berlaku harus sebelum / sama dengan Akhir Masa Berlaku!");
        } else if ($request->akhir_masa_berlaku < $request->awal_masa_berlaku) {
            return back()->withErrors("Tanggal Akhir Masa Berlaku harus sesudah / sama dengan Awal Masa Berlaku!");
        }

        try {
            DB::beginTransaction();


            $pelatih = Customer::find($request->id);

            if (empty($pelatih))
                $pelatih = new Customer();

            $pelatih->name = $request->name;
            $pelatih->phone = $request->phone;
            $pelatih->awal_masa_berlaku = $request->awal_masa_berlaku;
            $pelatih->akhir_masa_berlaku = $request->akhir_masa_berlaku;
            $pelatih->clubhouse_id = $request->clubhouse_id;
            $pelatih->id_club_renang = $request->clubhouse_id;
            $pelatih->is_pelatih = true;
            $pelatih->save();

            DB::commit();

            return redirect()->route('coach')->with([
                'success' => true,
                'action' => 'add'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Database error: ' . $e->getMessage()])->withInput();
        }
    }



    public function delete($id)
    {
        $coach = Customer::find($id);

        $coach->delete();

        return redirect()->route('coach')->with([
            'success' => true,
            'action' => 'delete'
        ]);
    }


}
