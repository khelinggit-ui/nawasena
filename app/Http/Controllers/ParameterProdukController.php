<?php

namespace App\Http\Controllers;

use App\Models\ParameterProduk;
use Illuminate\Http\Request;

class ParameterProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parameterProduks = ParameterProduk::latest()->get();
        return view('parameter-produk.index', compact('parameterProduks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('parameter-produk.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'bunga_persen' => 'required|numeric|min:0',
            'suku_bunga' => 'nullable|string|max:255',
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'bunga_persen.required' => 'Bunga persen wajib diisi.',
            'bunga_persen.numeric' => 'Bunga persen harus berupa angka.',
        ]);

        ParameterProduk::create($validated);

        return redirect()->route('parameter-produk.index')
            ->with('success', 'Parameter produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $parameterProduk = ParameterProduk::findOrFail($id);
        return view('parameter-produk.edit', compact('parameterProduk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $parameterProduk = ParameterProduk::findOrFail($id);

        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'bunga_persen' => 'required|numeric|min:0',
            'suku_bunga' => 'nullable|string|max:255',
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'bunga_persen.required' => 'Bunga persen wajib diisi.',
            'bunga_persen.numeric' => 'Bunga persen harus berupa angka.',
        ]);

        $parameterProduk->update($validated);

        return redirect()->route('parameter-produk.index')
            ->with('success', 'Parameter produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $parameterProduk = ParameterProduk::findOrFail($id);
        $parameterProduk->delete();

        return redirect()->route('parameter-produk.index')
            ->with('success', 'Parameter produk berhasil dihapus.');
    }
}
