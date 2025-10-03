<?php

namespace App\Http\Controllers\Front\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Carbon\Carbon;

class MemberViewController extends Controller
{
    public function viewMemberIndex(Request $request)
    {
        // ✅ Validasi filter
        $request->validate([
            'name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date_format:Y-m-d',
            'awal_masa' => 'nullable|date_format:Y-m-d',
            'akhir_masa' => 'nullable|date_format:Y-m-d|after_or_equal:awal_masa',
            'active' => 'nullable|in:0,1',
        ]);

        $today = now()->startOfDay();

        // ✅ Query members dengan filter
        $members = Customer::query()
            ->whereNotNull('member_id')
            ->whereHas('tiketTerbaru', function ($q) use ($request, $today) {
                if ($request->active === "1") { // hanya aktif
                    $q->whereDate('date_end', '>=', $today)
                      ->where('code', 'LIKE', 'M%');
                }

                if ($request->active === "0") { // hanya non-aktif/expired
                    $q->whereDate('date_end', '<', $today)
                      ->where('code', 'LIKE', 'M%');
                }

                if ($request->awal_masa) {
                    $q->whereDate('date_start', '>=', Carbon::parse($request->awal_masa));
                }
                if ($request->akhir_masa) {
                    $q->whereDate('date_end', '<=', Carbon::parse($request->akhir_masa));
                }
            })
            ->when($request->name, fn($q, $name) => $q->where('name', 'LIKE', "%$name%"))
            ->when($request->dob, fn($q, $dob) => $q->whereDate('dob', Carbon::parse($dob)->startOfDay()))
            ->when($request->phone, fn($q, $phone) => $q->where('phone', $phone))
            ->with([
                'tiketTerbaru',
                'tickets' => fn($q) => $q->where('code', 'LIKE', 'M%')->orderBy('created_at', 'desc'),
            ])
            ->get();

        // ✅ Hitung total
        $totalActive = $members->filter(function ($m) use ($today) {
            return $m->tiketTerbaru && $m->tiketTerbaru->date_end >= $today;
        })->count();

        $totalAll = $members->count();

        return view('front.admin.viewMember', [
            'members' => $members,
            'totalActive' => $totalActive,
            'totalAll' => $totalAll,
        ]);
    }
}
