<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('manage users');

        $users = User::with('roles', 'anggota')->orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('manage users');

        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage users');

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'roles'    => 'array',
        ]);

        /** ======================
         * 1. CREATE USER
         * ====================== */
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        /** ======================
         * 2. ASSIGN ROLE
         * ====================== */
        if ($request->filled('roles')) {
            $user->syncRoles($request->roles);
        }

        /** ======================
         * 3. AUTO CREATE ANGGOTA
         * ====================== */
        if ($user->hasRole('anggota')) {
            Anggota::create([
                'user_id'        => $user->id,
                'nomor_anggota'  => $this->generateNomorAnggota(),
                'nama'           => $user->name,
                'status'         => 'aktif',
                'tanggal_masuk'  => now(),
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        $this->authorize('manage users');

        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('manage users');

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'roles' => 'array',
        ]);

        /** ======================
         * 1. UPDATE USER
         * ====================== */
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        /** ======================
         * 2. SYNC ROLES
         * ====================== */
        // simpan role lama
        $oldRoles = $user->roles->pluck('name')->toArray();

        // update user
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        // sync role
        $user->syncRoles($request->roles ?? []);
        $user->load('roles');

        $newRoles = $user->roles->pluck('name')->toArray();

        $oldHasAnggota = in_array('anggota', $oldRoles);
        $newHasAnggota = in_array('anggota', $newRoles);

        // sinkron anggota
        $anggota = Anggota::where('user_id', $user->id)->first();

        if ($anggota) {
            $anggota->update([
                'nama' => $user->name,
            ]);
        }

        // role anggota ditambahkan
        if (! $oldHasAnggota && $newHasAnggota) {
            Anggota::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nomor_anggota' => $this->generateNomorAnggota(),
                    'nama'          => $user->name,
                    'status'        => 'aktif',
                    'tanggal_masuk' => now(),
                ]
            );
        }

        // role anggota dihapus
        if ($oldHasAnggota && ! $newHasAnggota && $anggota) {
            $anggota->update(['status' => 'keluar']);
        }


        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui');
    }

    /**
     * ======================
     * GENERATE NOMOR ANGGOTA
     * ======================
     */
    private function generateNomorAnggota(): string
    {
        $lastId = Anggota::max('id') ?? 0;

        return 'AG-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
    }
}
