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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'clubhouse_id' => 'nullable|integer',
        ]);

        // Query mencari nama coach
        $pelatih = Customer::query()
            ->where('is_pelatih', "1")
            ->when($request->search_name, fn($q, $name) => $q->where('name', 'LIKE', "%$name%"))
            ->when($request->start_date, fn($q, $date) => $q->whereDate('awal_masa_berlaku', '>=', $date))
            ->when($request->end_date, fn($q, $date) => $q->whereDate('akhir_masa_berlaku', '<=', $date))
            ->when($request->clubhouse_id, function ($q, $clubhouseId) {
                // Check both clubhouse_id and id_club_renang for robustness
                $q->where(function ($sub) use ($clubhouseId) {
                    $sub->where('clubhouse_id', $clubhouseId)
                        ->orWhere('id_club_renang', $clubhouseId);
                });
            })
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
        try {
            DB::beginTransaction();

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
                throw new \Exception("Tanggal Awal Masa Berlaku harus sebelum / sama dengan Akhir Masa Berlaku!");
            }

            $pelatih = new Customer();
            $pelatih->name = $request->name;
            $pelatih->phone = $request->phone;
            $pelatih->awal_masa_berlaku = $request->awal_masa_berlaku;
            $pelatih->akhir_masa_berlaku = $request->akhir_masa_berlaku;
            $pelatih->clubhouse_id = $request->clubhouse_id;
            $pelatih->id_club_renang = $request->clubhouse_id;
            $pelatih->is_pelatih = true;
            
            if (!$pelatih->save()) {
                throw new \Exception("Gagal menyimpan data Pelatih.");
            }

            DB::commit();

            return redirect()->route('coach')->with([
                'success' => true,
                'action' => 'add'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
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
        try {
            DB::beginTransaction();

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
                throw new \Exception("Tanggal Awal Masa Berlaku harus sebelum / sama dengan Akhir Masa Berlaku!");
            }

            $pelatih = Customer::findOrFail($id);
            $pelatih->name = $request->name;
            $pelatih->phone = $request->phone;
            $pelatih->awal_masa_berlaku = $request->awal_masa_berlaku;
            $pelatih->akhir_masa_berlaku = $request->akhir_masa_berlaku;
            $pelatih->clubhouse_id = $request->clubhouse_id;
            $pelatih->id_club_renang = $request->clubhouse_id;
            $pelatih->is_pelatih = true;
            
            if (!$pelatih->save()) {
                throw new \Exception("Gagal memperbarui data Pelatih.");
            }

            DB::commit();

            return redirect()->route('coach')->with([
                'success' => true,
                'action' => 'edit'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
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
