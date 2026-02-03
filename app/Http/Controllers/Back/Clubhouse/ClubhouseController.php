<?php

namespace App\Http\Controllers\Back\Clubhouse;

use App\Http\Controllers\Controller;
use App\Models\Clubhouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClubhouseController extends Controller
{
    public function index(Request $request){
        $clubhouses = Clubhouse::paginate(10);

        return view('back.clubhouse.index', compact('clubhouses'));
    }

    public function add(Request $request){
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'phone' => 'required|regex:/^08[1-9][0-9]{6,10}$/',
            ], [
                'name.required' => 'Nama Clubhouse harus diisi!',
                'location.required' => 'Lokasi Clubhouse harus diisi!',
                'phone.required' => 'Nomor Telepon harus diisi!',
                'phone.regex' => 'Format Nomor Telepon tidak valid! (Gunakan format 08...)'
            ]);

            $clubhouse = new Clubhouse();
            $clubhouse->name = $request->name;
            $clubhouse->location = $request->location;
            $clubhouse->phone = $request->phone;
            
            if (!$clubhouse->save()) {
                throw new \Exception("Gagal menyimpan data Clubhouse.");
            }

            DB::commit();

            return redirect()->route('clubhouse')->with([
                'success' => true,
                'action' => 'add'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function getClubhouse($id){
        $clubhouse = Clubhouse::find($id);
        return response()->json($clubhouse);
    }

    public function edit(Request $request, $id){
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'phone' => 'required|regex:/^08[1-9][0-9]{6,10}$/',
            ], [
                'name.required' => 'Nama Clubhouse harus diisi!',
                'location.required' => 'Lokasi Clubhouse harus diisi!',
                'phone.required' => 'Nomor Telepon harus diisi!',
                'phone.regex' => 'Format Nomor Telepon tidak valid! (Gunakan format 08...)'
            ]);

            $clubhouse = Clubhouse::findOrFail($id);
            $clubhouse->name = $request->name;
            $clubhouse->location = $request->location;
            $clubhouse->phone = $request->phone;
            
            if (!$clubhouse->save()) {
                throw new \Exception("Gagal memperbarui data Clubhouse.");
            }

            DB::commit();

            return redirect()->route('clubhouse')->with([
                'success' => true,
                'action' => 'edit'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }


    public function delete($id){
        $clubhouse = Clubhouse::find($id);
        $clubhouse->delete();

        return redirect()->route('clubhouse')->with([
            'success' => true,
            'action' => 'delete'
        ]);
    }
}
