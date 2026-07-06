<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'unitKerja'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('nama')->get();
        $unitKerjas = UnitKerja::orderBy('nama')->get();
        return view('admin.users.create', compact('roles', 'unitKerjas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|max:18|unique:users',
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id',
            'unit_kerja_id' => 'nullable|exists:unit_kerjas,id',
            'storage_quota' => 'required|integer|min:0',
        ]);

        User::create([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'unit_kerja_id' => $request->unit_kerja_id,
            'storage_quota' => $request->storage_quota,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('nama')->get();
        $unitKerjas = UnitKerja::orderBy('nama')->get();
        return view('admin.users.edit', compact('user', 'roles', 'unitKerjas'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nip' => 'required|string|max:18|unique:users,nip,' . $user->id,
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:8',
            'role_id' => 'required|exists:roles,id',
            'unit_kerja_id' => 'nullable|exists:unit_kerjas,id',
            'storage_quota' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $data = [
            'nip' => $request->nip,
            'nama' => $request->nama,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'unit_kerja_id' => $request->unit_kerja_id,
            'storage_quota' => $request->storage_quota,
            'is_active' => $request->is_active,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
