<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnitKerja;
use Illuminate\Http\Request;

class UnitKerjaController extends Controller
{
    public function index()
    {
        $unitKerjas = UnitKerja::orderBy('nama')->paginate(20);
        return view('admin.unit-kerja.index', compact('unitKerjas'));
    }

    public function create()
    {
        return view('admin.unit-kerja.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:20|unique:unit_kerjas',
            'deskripsi' => 'nullable|string',
        ]);

        UnitKerja::create($request->all());

        return redirect()->route('admin.unit-kerja.index')->with('success', 'Unit Kerja created successfully.');
    }

    public function edit(UnitKerja $unitKerja)
    {
        return view('admin.unit-kerja.edit', compact('unitKerja'));
    }

    public function update(Request $request, UnitKerja $unitKerja)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:20|unique:unit_kerjas,kode,' . $unitKerja->id,
            'deskripsi' => 'nullable|string',
        ]);

        $unitKerja->update($request->all());

        return redirect()->route('admin.unit-kerja.index')->with('success', 'Unit Kerja updated successfully.');
    }

    public function destroy(UnitKerja $unitKerja)
    {
        if ($unitKerja->users()->exists()) {
            return back()->with('error', 'Cannot delete Unit Kerja as it has associated users.');
        }

        $unitKerja->delete();
        return redirect()->route('admin.unit-kerja.index')->with('success', 'Unit Kerja deleted successfully.');
    }
}
