<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Dinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAkunDinasController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if ($admin && !$admin->isSuperAdmin()) {
            return redirect()->route('admin.dashboard')->with('error', 'Halaman Manajemen Akun hanya dapat diakses oleh Super Admin.');
        }

        $keyword = trim((string) $request->query('keyword', ''));
        $dinasFilter = (string) $request->query('dinas', 'semua');
        $statusFilter = (string) $request->query('status', 'semua');

        $akunList = Admin::with('dinas')
            ->whereIn('role', ['dinas', 'admin_dinas', 'superadmin', 'super_admin'])
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($search) use ($keyword) {
                    $search->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('username', 'like', "%{$keyword}%")
                        ->orWhere('role', 'like', "%{$keyword}%")
                        ->orWhereHas('dinas', function ($q) use ($keyword) {
                            $q->where('nama_dinas', 'like', "%{$keyword}%")
                                ->orWhere('singkatan', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($dinasFilter !== 'semua', function ($query) use ($dinasFilter) {
                if ($dinasFilter === 'superadmin') {
                    $query->whereIn('role', ['superadmin', 'super_admin']);
                } else {
                    $query->where('id_dinas', $dinasFilter);
                }
            })
            ->when($statusFilter !== 'semua', fn ($query) => $query->where('status', $statusFilter))
            ->latest('id_admin')
            ->get();

        $totalAkun = Admin::whereIn('role', ['dinas', 'admin_dinas', 'superadmin', 'super_admin'])->count();
        $totalAktif = Admin::whereIn('role', ['dinas', 'admin_dinas', 'superadmin', 'super_admin'])->where('status', 'aktif')->count();
        $totalNonaktif = Admin::whereIn('role', ['dinas', 'admin_dinas', 'superadmin', 'super_admin'])->where('status', 'nonaktif')->count();
        $masterDinas = Dinas::orderBy('nama_dinas')->get();

        return view('admin.akun.dinas.index', compact(
            'admin',
            'akunList',
            'totalAkun',
            'totalAktif',
            'totalNonaktif',
            'keyword',
            'dinasFilter',
            'statusFilter',
            'masterDinas'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:sirapi_md_admin,username',
            'password' => 'required|string|min:6',
            'id_dinas' => 'required',
        ]);

        $idDinasInput = $request->input('id_dinas');
        $isSuperAdmin = ($idDinasInput === 'superadmin');

        if (!$isSuperAdmin) {
            $request->validate([
                'id_dinas' => 'exists:sirapi_md_dinas,id_dinas',
            ]);
        }

        Admin::create([
            'nama' => $request->input('nama'),
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
            'role' => $isSuperAdmin ? 'superadmin' : 'dinas',
            'id_dinas' => $isSuperAdmin ? null : (int) $idDinasInput,
            'status' => 'aktif',
        ]);

        return back()->with('success', 'Akun Admin berhasil dibuat.');
    }

    public function update($id, Request $request)
    {
        $akun = Admin::whereIn('role', ['dinas', 'admin_dinas', 'superadmin', 'super_admin'])->findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:sirapi_md_admin,username,' . $id . ',id_admin',
            'id_dinas' => 'required',
            'password' => 'nullable|string|min:6',
            'status' => 'nullable|in:aktif,nonaktif',
        ]);

        $idDinasInput = $request->input('id_dinas');
        $isSuperAdmin = ($idDinasInput === 'superadmin');

        if (!$isSuperAdmin) {
            $request->validate([
                'id_dinas' => 'exists:sirapi_md_dinas,id_dinas',
            ]);
        }

        $data = [
            'nama' => $request->input('nama'),
            'username' => $request->input('username'),
            'role' => $isSuperAdmin ? 'superadmin' : 'dinas',
            'id_dinas' => $isSuperAdmin ? null : (int) $idDinasInput,
            'status' => $request->input('status', $akun->status ?? 'aktif'),
        ];

        if (!empty($request->input('password'))) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $akun->update($data);

        return back()->with('success', 'Data Akun Admin berhasil diperbarui.');
    }

    public function resetPassword($id, Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $akun = Admin::whereIn('role', ['dinas', 'admin_dinas', 'superadmin', 'super_admin'])->findOrFail($id);
        $akun->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        return back()->with('success', 'Password akun Admin berhasil direset.');
    }

    public function destroy($id)
    {
        if (Auth::guard('admin')->id() == $id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        Admin::whereIn('role', ['dinas', 'admin_dinas', 'superadmin', 'super_admin'])->findOrFail($id)->delete();

        return back()->with('success', 'Akun Admin berhasil dihapus.');
    }
}
