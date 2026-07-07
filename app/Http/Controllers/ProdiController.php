<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProdiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prodis = Prodi::with(['fakultas'])->withCount('students')->orderBy('created_at', 'desc')->get();
        $fakultas = \App\Models\Fakultas::orderBy('nama_fakultas', 'asc')->get();
        return view('prodi.index', compact('prodis', 'fakultas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_prodi' => ['required', 'string', 'max:255'],
            'kode_prodi' => ['required', 'string', 'max:50', 'unique:prodis,kode_prodi'],
            'short_name' => ['required', 'string', 'max:50'],
            'fakultas_id' => ['nullable', 'exists:fakultas,id'],
        ]);

        Prodi::create([
            'nama_prodi' => $validated['nama_prodi'],
            'kode_prodi' => $validated['kode_prodi'],
            'short_name' => $validated['short_name'],
            'fakultas_id' => $validated['fakultas_id'] ?? null,
        ]);

        return back()->with('success', 'Program Studi berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $prodi = Prodi::findOrFail($id);

        $validated = $request->validate([
            'nama_prodi' => ['required', 'string', 'max:255'],
            'kode_prodi' => ['required', 'string', 'max:50', Rule::unique('prodis', 'kode_prodi')->ignore($prodi->id)],
            'short_name' => ['required', 'string', 'max:50'],
            'fakultas_id' => ['nullable', 'exists:fakultas,id'],
        ]);

        $prodi->update([
            'nama_prodi' => $validated['nama_prodi'],
            'kode_prodi' => $validated['kode_prodi'],
            'short_name' => $validated['short_name'],
            'fakultas_id' => $validated['fakultas_id'] ?? null,
        ]);

        return back()->with('success', 'Program Studi berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prodi = Prodi::findOrFail($id);

        // Check if there are students in this prodi
        if ($prodi->students()->count() > 0) {
            return back()->with('error', 'Program Studi tidak bisa dihapus karena masih memiliki data alumni terkait.');
        }

        $prodi->delete();

        return back()->with('success', 'Program Studi berhasil dihapus!');
    }
}
