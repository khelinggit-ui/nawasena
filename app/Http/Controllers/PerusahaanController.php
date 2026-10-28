<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perusahaans = Perusahaan::latest()->get();
        return view('perusahaan.index', compact('perusahaans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('perusahaan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'alamat_perusahaan' => 'nullable|string',
            'telepon_perusahaan' => 'nullable|string|max:50',
            'no_PKS' => 'nullable|string|max:255',
            'tanggal_PKS' => 'nullable|date',
            'tanggal_berakhir_PKS' => 'nullable|date',
            'payment' => 'nullable|string|max:255',
            'payroll_date' => 'nullable|string|max:255', // Or keep as integer depending on db type, treating as string for now
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        Perusahaan::create($data);

        return redirect()->route('perusahaan.index')->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $perusahaan = Perusahaan::findOrFail($id);
        return view('perusahaan.show', compact('perusahaan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $perusahaan = Perusahaan::findOrFail($id);
        return view('perusahaan.edit', compact('perusahaan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $perusahaan = Perusahaan::findOrFail($id);

        $data = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'alamat_perusahaan' => 'nullable|string',
            'telepon_perusahaan' => 'nullable|string|max:50',
            'no_PKS' => 'nullable|string|max:255',
            'tanggal_PKS' => 'nullable|date',
            'tanggal_berakhir_PKS' => 'nullable|date',
            'payment' => 'nullable|string|max:255',
            'payroll_date' => 'nullable|string|max:255',
        ]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $perusahaan->update($data);

        return redirect()->route('perusahaan.index')->with('success', 'Perusahaan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $perusahaan = Perusahaan::findOrFail($id);
        $perusahaan->delete();

        return redirect()->route('perusahaan.index')->with('success', 'Perusahaan berhasil dihapus.');
    }
}
