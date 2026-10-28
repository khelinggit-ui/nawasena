<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['jabatan', 'cabang', 'kantor', 'roles'])->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $cabangs = \App\Models\Cabang::all();
        $kantors = \App\Models\Kantor::orderBy('nama_kantor')->get();
        $jabatans = \App\Models\Jabatan::all();
        
        // Fetch SPVs
        $spvJabatan = \App\Models\Jabatan::where('nama_jabatan', 'like', '%SPV%')->first();
        $spvs = $spvJabatan ? User::where('jabatan_id', $spvJabatan->id)->get(['id', 'name', 'cabang_id']) : collect();
        if ($spvs->isEmpty()) {
            // fallback, try to get users with role spv if available
            $spvs = User::role('spv')->get(['id', 'name', 'cabang_id']);
        }

        return view('users.create', compact('roles', 'cabangs', 'kantors', 'jabatans', 'spvs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'jabatan_id' => 'required',
            'cabang_id' => 'required',
            'kantor_id' => 'nullable|exists:kantors,id',
            'status' => 'required|in:Active,Inactive',
            'roles' => 'required|array'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jabatan_id' => $request->jabatan_id,
            'cabang_id' => $request->cabang_id,
            'spv_id' => $request->spv_id,
            'kantor_id' => $request->kantor_id,
            'status' => $request->status,
            'role_name' => 'User',
        ]);

        $user->assignRole($request->roles);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $userRole = $user->roles->pluck('name')->toArray();
        $cabangs = \App\Models\Cabang::all();
        $kantors = \App\Models\Kantor::orderBy('nama_kantor')->get();
        $jabatans = \App\Models\Jabatan::all();
        
        $spvJabatan = \App\Models\Jabatan::where('nama_jabatan', 'like', '%SPV%')->first();
        $spvs = $spvJabatan ? User::where('jabatan_id', $spvJabatan->id)->get(['id', 'name', 'cabang_id']) : collect();
        if ($spvs->isEmpty()) {
            $spvs = User::role('spv')->get(['id', 'name', 'cabang_id']);
        }

        return view('users.edit', compact('user', 'roles', 'userRole', 'cabangs', 'kantors', 'jabatans', 'spvs'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'jabatan_id' => 'required',
            'cabang_id' => 'required',
            'kantor_id' => 'nullable|exists:kantors,id',
            'status' => 'required|in:Active,Inactive',
            'roles' => 'required|array'
        ]);

        $data = $request->only(['name', 'email', 'jabatan_id', 'cabang_id', 'spv_id', 'kantor_id', 'status']);
        if($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles($request->roles);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
