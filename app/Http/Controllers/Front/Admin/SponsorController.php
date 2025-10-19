<?php

namespace App\Http\Controllers\Front\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\CashSession;
use App\Models\Sponsor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SponsorController extends Controller
{
    /**
     * Tampilkan daftar sponsor.
     */
    public function index(Request $request)
    {
        $staff = Auth::guard('fo')->user();
        $today = now()->startOfDay();

        $query = Sponsor::query()->whereNull('deleted_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sponsors = $query->latest()->paginate(10);

        // --- Logic Cash Session (Tidak Diubah) ---
        $cashSessionQuery = CashSession::where('staff_id', $staff->id)
            ->whereDate('waktu_buka', $today)
            ->where('status', 1)
            ->latest();

        $cashSession = $cashSessionQuery->first();

        if (!$cashSession) {
            $cashSession = new CashSession([
                'saldo_awal' => 0,
                'waktu_buka' => null,
                'status' => 0,
            ]);
        }
        // ------------------------------------------

        return view('front.admin.sponsor', [
            'sponsors' => $sponsors,
            'cashSession' => $cashSession,
            'staff' => $staff
        ]);
    }

    /**
     * Menyimpan Sponsor baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Menggunakan Storage::disk('public')->putFile untuk kepastian path
            // Path akan disimpan sebagai 'sponsors/namafile.jpg' di dalam disk 'public'
            $imagePath = $request->file('image')->store('sponsors', 'public');
        }

        Sponsor::create([
            'name' => $request->name,
            'status' => $request->status,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.sponsor')->with([
            'success' => true,
            'action' => 'add'
        ]);
    }
    public function show(Sponsor $sponsor , $id)
    {
        $sponsor = Sponsor::findOrFail($id);
        
        // Bangun URL gambar dari folder publik
        $imageUrl = $sponsor->image ? asset('storage/' . ltrim($sponsor->image, '/')) : null;

        // Format tanggal dalam ISO8601 agar bisa langsung dipakai di JS
        $createdAtIso = $sponsor->created_at ? $sponsor->created_at->toIso8601String() : null;

        return response()->json([
            'id' => $sponsor->id,
            'name' => $sponsor->name,
            'status' => (int) $sponsor->status,
            'image_url' => $imageUrl,
            'created_at' => $createdAtIso,
        ]);
    }


    /**
     * Memperbarui Sponsor yang ada.
     */
    public function update(Request $request, Sponsor $sponsor, $id)
    {
        $sponsor = Sponsor::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = $sponsor->image;
        if ($request->hasFile('image')) {
            // Hapus gambar lama dari disk 'public'
            if ($sponsor->image) {
                Storage::disk('public')->delete($sponsor->image);
            }
            // Simpan gambar baru ke disk 'public'
            $imagePath = $request->file('image')->store('sponsors', 'public');
        }

        $sponsor->update([
            'name' => $request->name,
            'status' => $request->status,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.sponsor')->with([
            'success' => true,
            'action' => 'edit'
        ]);
    }

    /**
     * Menghapus Sponsor.
     */
    public function destroy(Sponsor $sponsor, $id)
    {
        $sponsor = Sponsor::findOrFail($id);
        // Hapus gambar dari disk 'public' sebelum soft delete
        if ($sponsor->image) {
            Storage::disk('public')->delete($sponsor->image);
        }

        // Soft delete
        $sponsor->delete();

        return redirect()->route('admin.sponsor')->with([
            'success' => true,
            'action' => 'delete'
        ]);
    }
}
