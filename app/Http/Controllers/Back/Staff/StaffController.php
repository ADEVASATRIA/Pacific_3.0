<?php

namespace App\Http\Controllers\Back\Staff;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $isActive = $request->input('is_active');

        $query = Admin::query();

        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', (bool) $isActive);
        }

        $admins = $query->where('deleted_at', null)->orderBy('created_at', 'asc')->get();
        return view('back.staff.index', compact('admins'));
    }

    public function add(Request $request)
    {
        $validatedFields = $request->validate([
            'username' => 'required|string|unique:admins,username',
            'password' => 'required|string|min:6',
            'tipe' => 'required|integer|in:1,2',
            'is_active' => 'required|integer|in:0,1',
            'pin' => 'nullable|digits:4|numeric',
        ]);

        // dd('masuk sini', $request->all(), $validatedFields);

        try {
            DB::beginTransaction();

            // --- Validasi tambahan untuk PIN ---
            if ($request->tipe == 1 && empty($request->pin)) {
                return redirect()->back()->withErrors('PIN wajib diisi untuk tipe 3!');
            }

            if (!empty($request->pin)) {
                // Pastikan PIN hanya angka dan 4 digit
                if (!preg_match('/^\d{4}$/', $request->pin)) {
                    return redirect()->back()->withErrors('PIN harus berupa 4 digit angka.');
                }

                // Cek apakah PIN sudah digunakan sebelumnya
                $pinExists = Admin::where('pin', $request->pin)->exists();
                if ($pinExists) {
                    return redirect()->back()->withErrors('PIN ini sudah digunakan. Silakan gunakan PIN lain.');
                }
            }

            // --- Buat instance baru admin/staff ---
            $staff = new Admin();
            $staff->username = $validatedFields['username'];
            $staff->name = $validatedFields['username'];
            $staff->password = bcrypt($validatedFields['password']);
            $staff->is_active = $validatedFields['is_active'];
            $staff->pin = $request->pin;

            // --- Logika tipe user ---
            $staff->is_staff = $request->tipe == 1 ? 1 : 0;
            $staff->is_admin = $request->tipe == 2 ? 1 : 0;

            $staff->save();

            // dd($staff);
            DB::commit();

            return redirect()->route('staff')->with([
                'success' => true,
                'action' => 'add'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->route('staff')->with([
                'success' => false,
                'action' => 'add',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function getAdmin($id)
    {
        $admin = Admin::findOrFail($id);
        return response()->json($admin);
    }

    public function edit(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validatedFields = $request->validate([
            'username' => 'required|string|unique:admins,username,' . $id,
            'password' => 'nullable|string|min:6',
            'tipe' => 'required|integer|in:1,2',
            'is_active' => 'required|integer|in:0,1',
            'pin' => 'nullable|digits:4|numeric',
        ]);

        try {
            DB::beginTransaction();

            // --- Validasi tambahan untuk PIN ---
            if ($request->tipe == 1 && empty($request->pin)) {
                return redirect()->back()->withErrors('PIN wajib diisi untuk tipe Kasir!');
            }

            if (!empty($request->pin)) {
                if (!preg_match('/^\d{4}$/', $request->pin)) {
                    return redirect()->back()->withErrors('PIN harus berupa 4 digit angka.');
                }

                $pinExists = Admin::where('pin', $request->pin)->where('id', '!=', $id)->exists();
                if ($pinExists) {
                    return redirect()->back()->withErrors('PIN ini sudah digunakan. Silakan gunakan PIN lain.');
                }
            }

            // --- Logika tipe user ---
            $admin->is_staff = $request->tipe == 1 ? 1 : 0;
            $admin->is_admin = $request->tipe == 2 ? 1 : 0;

            // --- Update data ---
            $admin->username = $validatedFields['username'];
            $admin->is_active = $validatedFields['is_active'];
            $admin->pin = $request->pin;

            if (!empty($validatedFields['password'])) {
                $admin->password = bcrypt($validatedFields['password']);
            }

            $admin->save();
            DB::commit();

            return redirect()->route('staff')->with([
                'success' => true,
                'action' => 'edit'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('staff')->with([
                'success' => false,
                'action' => 'edit',
                'error' => $th->getMessage()
            ]);
        }
    }


    public function delete($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();
        return redirect()->route('staff')->with([
            'success' => true,
            'action' => 'delete'
        ]);
    }

}
