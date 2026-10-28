<?php

namespace App\Http\Controllers;

use App\Models\Kantor;
use Illuminate\Http\Request;

class KantorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:page kantor|view kantor', ['only' => ['index']]);
        $this->middleware('permission:create kantor', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit kantor|update kantor', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete kantor', ['only' => ['destroy']]);
    }

    public function index()
    {
        $kantors = Kantor::latest()->get();
        return view('kantors.index', compact('kantors'));
    }

    public function create()
    {
        return view('kantors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kantor' => 'required|string|max:255|unique:kantors,nama_kantor',
            'unit' => 'nullable|in:NSB,HAEDA',
            'alamat' => 'nullable|string',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        Kantor::create($data);

        return redirect()->route('kantors.index')->with('success', 'Kantor berhasil ditambahkan.');
    }

    public function edit(Kantor $kantor)
    {
        return view('kantors.edit', compact('kantor'));
    }

    public function update(Request $request, Kantor $kantor)
    {
        $data = $request->validate([
            'nama_kantor' => 'required|string|max:255|unique:kantors,nama_kantor,' . $kantor->id,
            'unit' => 'nullable|in:NSB,HAEDA',
            'alamat' => 'nullable|string',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        $kantor->update($data);

        return redirect()->route('kantors.index')->with('success', 'Kantor berhasil diperbarui.');
    }

    public function destroy(Kantor $kantor)
    {
        $kantor->delete();

        return redirect()->route('kantors.index')->with('success', 'Kantor berhasil dihapus.');
    }
}
