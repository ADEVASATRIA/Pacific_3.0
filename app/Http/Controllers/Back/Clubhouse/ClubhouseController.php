<?php

namespace App\Http\Controllers\Back\Clubhouse;

use App\Http\Controllers\Controller;
use App\Models\Clubhouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                'dokumen_pdf' => 'nullable|file|mimes:pdf|max:5120',
                'ktp_pengurus' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ], [
                'name.required' => 'Nama Clubhouse harus diisi!',
                'location.required' => 'Lokasi Clubhouse harus diisi!',
                'phone.required' => 'Nomor Telepon harus diisi!',
                'phone.regex' => 'Format Nomor Telepon tidak valid! (Gunakan format 08...)',
                'dokumen_pdf.mimes' => 'Dokumen Clubhouse harus berupa file PDF!',
                'dokumen_pdf.max' => 'Ukuran dokumen maksimal 5MB!',
                'ktp_pengurus.mimes' => 'KTP Pengurus harus berupa file PDF/JPG/PNG!',
                'ktp_pengurus.max' => 'Ukuran file KTP maksimal 5MB!',
            ]);

            $clubhouse = new Clubhouse();
            $clubhouse->name = $request->name;
            $clubhouse->location = $request->location;
            $clubhouse->phone = $request->phone;

            if ($request->hasFile('dokumen_pdf')) {
                $clubhouse->dokumen_pdf = $request->file('dokumen_pdf')->store('clubhouse_documents', 'public');
            }

            if ($request->hasFile('ktp_pengurus')) {
                $clubhouse->ktp_pengurus = $request->file('ktp_pengurus')->store('clubhouse_ktp', 'public');
            }

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

        $data = $clubhouse->toArray();
        $data['dokumen_pdf_url'] = $clubhouse->dokumen_pdf
            ? asset('storage/' . ltrim($clubhouse->dokumen_pdf, '/'))
            : null;
        $data['ktp_pengurus_url'] = $clubhouse->ktp_pengurus
            ? asset('storage/' . ltrim($clubhouse->ktp_pengurus, '/'))
            : null;

        return response()->json($data);
    }

    public function edit(Request $request, $id){
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'phone' => 'required|regex:/^08[1-9][0-9]{6,10}$/',
                'dokumen_pdf' => 'nullable|file|mimes:pdf|max:5120',
                'ktp_pengurus' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ], [
                'name.required' => 'Nama Clubhouse harus diisi!',
                'location.required' => 'Lokasi Clubhouse harus diisi!',
                'phone.required' => 'Nomor Telepon harus diisi!',
                'phone.regex' => 'Format Nomor Telepon tidak valid! (Gunakan format 08...)',
                'dokumen_pdf.mimes' => 'Dokumen Clubhouse harus berupa file PDF!',
                'dokumen_pdf.max' => 'Ukuran dokumen maksimal 5MB!',
                'ktp_pengurus.mimes' => 'KTP Pengurus harus berupa file PDF/JPG/PNG!',
                'ktp_pengurus.max' => 'Ukuran file KTP maksimal 5MB!',
            ]);

            $clubhouse = Clubhouse::findOrFail($id);
            $clubhouse->name = $request->name;
            $clubhouse->location = $request->location;
            $clubhouse->phone = $request->phone;

            if ($request->hasFile('dokumen_pdf')) {
                if ($clubhouse->dokumen_pdf) {
                    Storage::disk('public')->delete($clubhouse->dokumen_pdf);
                }
                $clubhouse->dokumen_pdf = $request->file('dokumen_pdf')->store('clubhouse_documents', 'public');
            }

            if ($request->hasFile('ktp_pengurus')) {
                if ($clubhouse->ktp_pengurus) {
                    Storage::disk('public')->delete($clubhouse->ktp_pengurus);
                }
                $clubhouse->ktp_pengurus = $request->file('ktp_pengurus')->store('clubhouse_ktp', 'public');
            }

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

        if ($clubhouse->dokumen_pdf) {
            Storage::disk('public')->delete($clubhouse->dokumen_pdf);
        }
        if ($clubhouse->ktp_pengurus) {
            Storage::disk('public')->delete($clubhouse->ktp_pengurus);
        }

        $clubhouse->delete();

        return redirect()->route('clubhouse')->with([
            'success' => true,
            'action' => 'delete'
        ]);
    }
}
