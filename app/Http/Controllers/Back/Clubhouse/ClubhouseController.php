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
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'phone' => 'required|regex:/^08[1-9][0-9]{6,10}$/',
        ]);

        try {
            DB::beginTransaction();

            $clubhouse = new Clubhouse();
            $clubhouse->name = $request->name;
            $clubhouse->location = $request->location;
            $clubhouse->phone = $request->phone;
            $clubhouse->save();

            DB::commit();

            return redirect()->route('clubhouse')->with([
                'success' => true,
                'action' => 'add'
            ]);


        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('clubhouse')->with([
                'error' => true,
                'action' => 'add'
            ]);
        }
    }

    public function getClubhouse($id){
        $clubhouse = Clubhouse::find($id);
        return response()->json($clubhouse);
    }

    public function edit(Request $request, $id){
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'phone' => 'required|regex:/^08[1-9][0-9]{6,10}$/',
        ]);

        try {
            DB::beginTransaction();

            $clubhouse = Clubhouse::find($id);
            $clubhouse->name = $request->name;
            $clubhouse->location = $request->location;
            $clubhouse->phone = $request->phone;
            $clubhouse->save();

            DB::commit();

            return redirect()->route('clubhouse')->with([
                'success' => true,
                'action' => 'edit'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('clubhouse')->with([
                'error' => true,
                'action' => 'edit'
            ]);
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
