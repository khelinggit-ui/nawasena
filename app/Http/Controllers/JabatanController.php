<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jabatans = Jabatan::orderBy('id', 'desc')->get();
        return view('jabatan.index', compact('jabatans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jabatan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatans,nama_jabatan',
        ], [
            'nama_jabatan.unique' => 'Nama jabatan sudah terdaftar.',
            'nama_jabatan.required' => 'Nama jabatan wajib diisi.'
        ]);

        Jabatan::create($data);

        return redirect()->route('jabatans.index')->with('success', 'Master Jabatan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // View show not usually needed for simple master data, redirect to index
        return redirect()->route('jabatans.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        return view('jabatan.edit', compact('jabatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $jabatan = Jabatan::findOrFail($id);

        $data = $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatans,nama_jabatan,'.$id,
        ]);

        $jabatan->update($data);

        return redirect()->route('jabatans.index')->with('success', 'Master Jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        // Check if role is assigned to users before deleting to prevent orphan records or just delete.
        // Assuming strict soft relationships, or we can just catch constraint if strict.
        if ($jabatan->users()->count() > 0) {
            return redirect()->route('jabatans.index')->with('error', 'Gagal menghapus! Jabatan ini sedang digunakan oleh User.');
        }

        $jabatan->delete();

        return redirect()->route('jabatans.index')->with('success', 'Master Jabatan berhasil dihapus.');
    }
}
