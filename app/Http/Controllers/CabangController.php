<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:page cabang|view cabang', ['only' => ['index']]);
        $this->middleware('permission:create cabang', ['only' => ['create','store']]);
        $this->middleware('permission:edit cabang|update cabang', ['only' => ['edit','update']]);
        $this->middleware('permission:delete cabang', ['only' => ['destroy']]);
    }

    public function index()
    {
        $cabangs = Cabang::all();
        return view('cabangs.index', compact('cabangs'));
    }

    public function create()
    {
        return view('cabangs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_cabang' => 'required|string|max:255',
            'spv_cabang' => 'required|string|max:255',
            'target_cabang' => 'required|numeric',
            'pencapaian_bulan_lalu' => 'required|numeric',
            'status' => 'required|string',
        ]);

        Cabang::create($request->all());

        return redirect()->route('cabangs.index')->with('success', 'Cabang created successfully.');
    }

    public function edit(Cabang $cabang)
    {
        return view('cabangs.edit', compact('cabang'));
    }

    public function update(Request $request, Cabang $cabang)
    {
        $request->validate([
            'nama_cabang' => 'required|string|max:255',
            'spv_cabang' => 'required|string|max:255',
            'target_cabang' => 'required|numeric',
            'pencapaian_bulan_lalu' => 'required|numeric',
            'status' => 'required|string',
        ]);

        $cabang->update($request->all());

        return redirect()->route('cabangs.index')->with('success', 'Cabang updated successfully.');
    }

    public function destroy(Cabang $cabang)
    {
        $cabang->delete();
        return redirect()->route('cabangs.index')->with('success', 'Cabang deleted successfully.');
    }
}
