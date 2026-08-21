<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        // Hanya pengguna dengan izin user.manage yang dapat mengakses
        $this->middleware('permission:user.manage');
    }

    /** Menampilkan daftar seluruh pengguna beserta peran masing-masing. */
    public function index()
    {
        return view('users.index', [
            'users' => User::with('roles')->paginate(10),
        ]);
    }

    /** Menampilkan form tambah pengguna baru. */
    public function create()
    {
        return view('users.form', [
            'user'  => new User,
            'roles' => Role::all(),
        ]);
    }

    /** Menyimpan pengguna baru dan menetapkan perannya. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|max:100',
            'email'    => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create($data);
        $user->syncRoles($data['role']);

        return to_route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /** Menampilkan form edit pengguna. */
    public function edit(User $user)
    {
        return view('users.form', [
            'user'  => $user,
            'roles' => Role::all(),
        ]);
    }

    /** Memperbarui data dan peran pengguna. */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role'     => 'required|exists:roles,name',
        ]);

        // Hapus field password jika tidak diisi (tidak ingin mengganti)
        if (blank($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles($data['role']);

        return to_route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /** Menghapus pengguna. Pengguna tidak dapat menghapus akunnya sendiri. */
    public function destroy(User $user)
    {
        abort_if($user->is(auth()->user()), 403, 'Anda tidak dapat menghapus akun Anda sendiri.');

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
