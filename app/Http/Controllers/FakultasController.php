<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\Univ;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FakultasController extends Controller
{
    public function index()
    {
        $fakultas = Fakultas::with(['univ'])->withCount('prodis')->orderBy('created_at', 'desc')->get();
        $univs = Univ::orderBy('nama_univ', 'asc')->get();

        return view('fakultas.index', compact('fakultas', 'univs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_fakultas' => ['required', 'string', 'max:50', 'unique:fakultas,kode_fakultas'],
            'nama_fakultas' => ['required', 'string', 'max:255'],
            'id_univs' => ['required', 'exists:univs,id'],
            'short_name' => ['required', 'string', 'max:50'],
        ]);

        Fakultas::create($validated);

        return back()->with('success', 'Fakultas berhasil ditambahkan!');
    }

    public function update(Request $request, string $id)
    {
        $fakultasInstance = Fakultas::findOrFail($id);

        $validated = $request->validate([
            'kode_fakultas' => ['required', 'string', 'max:50', Rule::unique('fakultas', 'kode_fakultas')->ignore($fakultasInstance->id)],
            'nama_fakultas' => ['required', 'string', 'max:255'],
            'id_univs' => ['required', 'exists:univs,id'],
            'short_name' => ['required', 'string', 'max:50'],
        ]);

        $fakultasInstance->update($validated);

        return back()->with('success', 'Fakultas berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $fakultasInstance = Fakultas::findOrFail($id);

        if ($fakultasInstance->prodis()->count() > 0) {
            return back()->with('error', 'Fakultas tidak dapat dihapus karena memiliki program studi yang terhubung.');
        }

        $fakultasInstance->delete();

        return back()->with('success', 'Fakultas berhasil dihapus!');
    }
}
