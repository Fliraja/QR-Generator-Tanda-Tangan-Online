<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|unique:users,nip',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'role' => 'required|in:staff,admin',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);
        return redirect()->route('users.index')->with('success', 'Akun staf berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:staff,admin',
        ]);
        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'Data akun diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->update(['is_active' => false]);
        return redirect()->route('users.index')->with('success', 'Akun dinonaktifkan.');
    }
}
