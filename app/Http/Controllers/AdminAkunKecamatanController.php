<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAkunKecamatanController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if ($admin && !$admin->isSuperAdmin()) {
            return redirect()->route('admin.dashboard')->with('error', 'Halaman Manajemen Akun hanya dapat diakses oleh Super Admin.');
        }

        $keyword = trim((string) $request->query('keyword', ''));
        $kecamatanFilter = (string) $request->query('kecamatan', 'semua');
        $statusFilter = (string) $request->query('status', 'semua');

        $akunList = Admin::with('kecamatan')
            ->whereIn('role', ['kecamatan', 'admin_kecamatan'])
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($search) use ($keyword) {
                    $search->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('username', 'like', "%{$keyword}%")
                        ->orWhereHas('kecamatan', function ($q) use ($keyword) {
                            $q->where('nama_kecamatan', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($kecamatanFilter !== 'semua', fn ($query) => $query->where('id_kecamatan', $kecamatanFilter))
            ->when($statusFilter !== 'semua', fn ($query) => $query->where('status', $statusFilter))
            ->latest('id_admin')
            ->get();

        $totalAkun = Admin::whereIn('role', ['kecamatan', 'admin_kecamatan'])->count();
        $totalAktif = Admin::whereIn('role', ['kecamatan', 'admin_kecamatan'])->where('status', 'aktif')->count();
        $totalNonaktif = Admin::whereIn('role', ['kecamatan', 'admin_kecamatan'])->where('status', 'nonaktif')->count();
        $masterKecamatan = Kecamatan::orderBy('nama_kecamatan')->get();

        return view('admin.akun.kecamatan.index', compact(
            'admin',
            'akunList',
            'totalAkun',
            'totalAktif',
            'totalNonaktif',
            'keyword',
            'kecamatanFilter',
            'statusFilter',
            'masterKecamatan'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:sirapi_md_admin,username',
            'password' => 'required|string|min:6',
            'id_kecamatan' => 'required|exists:sirapi_md_kecamatan,id_kecamatan',
        ]);

        Admin::create([
            'nama' => $request->input('nama'),
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
            'role' => 'kecamatan',
            'id_kecamatan' => (int) $request->input('id_kecamatan'),
            'status' => 'aktif',
        ]);

        return back()->with('success', 'Akun Kecamatan berhasil dibuat.');
    }

    public function update($id, Request $request)
    {
        $akun = Admin::whereIn('role', ['kecamatan', 'admin_kecamatan'])->findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:sirapi_md_admin,username,' . $id . ',id_admin',
            'id_kecamatan' => 'required|exists:sirapi_md_kecamatan,id_kecamatan',
            'password' => 'nullable|string|min:6',
            'status' => 'nullable|in:aktif,nonaktif',
        ]);

        $data = [
            'nama' => $request->input('nama'),
            'username' => $request->input('username'),
            'id_kecamatan' => (int) $request->input('id_kecamatan'),
            'status' => $request->input('status', $akun->status ?? 'aktif'),
        ];

        if (!empty($request->input('password'))) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $akun->update($data);

        return back()->with('success', 'Data Akun Kecamatan berhasil diperbarui.');
    }

    public function resetPassword($id, Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $akun = Admin::whereIn('role', ['kecamatan', 'admin_kecamatan'])->findOrFail($id);
        $akun->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        return back()->with('success', 'Password akun Kecamatan berhasil direset.');
    }

    public function destroy($id)
    {
        if (Auth::guard('admin')->id() == $id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        Admin::whereIn('role', ['kecamatan', 'admin_kecamatan'])->findOrFail($id)->delete();

        return back()->with('success', 'Akun Kecamatan berhasil dihapus.');
    }
}
