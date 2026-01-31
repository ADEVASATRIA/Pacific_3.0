<?php

namespace App\Http\Controllers\Back\Staff;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $nama = $request->input('nama');
        $isActive = $request->input('is_active');

        $query = Admin::query();

        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', (bool) $isActive);
        }

        if ($nama !== null && $nama !== '') {
            $query->where('name', 'like', '%' . $nama . '%');
        }

        $admins = $query->where('deleted_at', null)->orderBy('created_at', 'asc')->get();
        return view('back.staff.index', compact('admins'));
    }

    public function add(Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'username' => 'required|string|unique:admins,username',
                'password' => 'required|string|min:6',
                'tipe' => 'required|integer|in:1,2',
                'is_active' => 'required|integer|in:0,1',
                'pin' => 'nullable|digits:4|numeric',
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            // --- Validasi tambahan untuk PIN ---
            if ($request->tipe == 1 && empty($request->pin)) {
                throw new \Exception(
                    'PIN wajib diisi untuk tipe Staff!'
                );
            }

            if (!empty($request->pin)) {
                // Pastikan PIN hanya angka dan 4 digit
                if (!preg_match('/^\d{4}$/', $request->pin)) {
                    throw new \Exception(
                        'PIN harus berupa 4 digit angka.'
                    );
                }

                // Cek apakah PIN sudah digunakan sebelumnya
                $pinExists = Admin::where('pin', $request->pin)->exists();
                if ($pinExists) {
                    // dd('masuk sini');
                    throw new \Exception(
                        'Pin yang anda masukkan sudah digunakan. Silakan gunakan PIN lain.'
                    );
                }
            }

            // --- Buat instance baru admin/staff ---
            $staff = new Admin();
            $staff->username = $request->username;
            $staff->name = $request->username;
            $staff->password = bcrypt($request->password);
            $staff->is_active = $request->is_active;
            $staff->pin = $request->pin;

            // --- Logika tipe user ---
            $staff->is_staff = $request->tipe == 1 ? 1 : 0;
            $staff->is_admin = $request->tipe == 2 ? 1 : 0;

            $staff->save();

            // dd($staff);
            DB::commit();

            return redirect()->route('staff')->with([
                'success' => 'Berhasil menambahkan data staff!',
                'action' => 'add'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with([
                'success' => false,
                'action' => 'add',
                'error' => 'Terjadi kesalahan: ' . $th->getMessage()
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
        try {
            DB::beginTransaction();

            $admin = Admin::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'username' => 'required|string|unique:admins,username,' . $id,
                'password' => 'nullable|string|min:6',
                'tipe' => 'required|integer|in:1,2',
                'is_active' => 'required|integer|in:0,1',
                'pin' => 'nullable|digits:4|numeric',
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            // --- Validasi tambahan untuk PIN ---
            if ($request->tipe == 1 && empty($request->pin)) {
                throw new \Exception(
                    'PIN wajib diisi untuk tipe Staff!'
                );
            }

            if (!empty($request->pin)) {
                if (!preg_match('/^\d{4}$/', $request->pin)) {
                    throw new \Exception(
                        'PIN harus berupa 4 digit angka.'
                    );
                }

                $pinExists = Admin::where('pin', $request->pin)->where('id', '!=', $id)->exists();
                if ($pinExists) {
                    throw new \Exception(
                        'Pin yang anda masukkan sudah digunakan. Silakan gunakan PIN lain.'
                    );
                }
            }

            // --- Logika tipe user ---
            $admin->is_staff = $request->tipe == 1 ? 1 : 0;
            $admin->is_admin = $request->tipe == 2 ? 1 : 0;

            // --- Update data ---
            $admin->username = $request->username;
            $admin->is_active = $request->is_active;
            $admin->pin = $request->pin;

            if (!empty($request->password)) {
                $admin->password = bcrypt($request->password);
            }

            $admin->save();
            DB::commit();

            return redirect()->route('staff')->with([
                'success' => 'Berhasil mengedit data staff!',
                'action' => 'edit'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with([
                'success' => false,
                'action' => 'edit',
                'error' => 'Terjadi kesalahan: ' . $th->getMessage()
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
