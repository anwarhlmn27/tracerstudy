<?php

namespace App\Http\Controllers;

use App\Models\Univ;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnivController extends Controller
{
    public function index()
    {
        $univs = Univ::withCount('fakultas')->orderBy('created_at', 'desc')->get();

        return view('univ.index', compact('univs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_univ' => ['required', 'string', 'max:50', 'unique:univs,kode_univ'],
            'nama_univ' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255'],
            'website' => ['required', 'string', 'max:255'],
        ]);

        Univ::create($validated);

        return back()->with('success', 'Universitas berhasil ditambahkan!');
    }

    public function update(Request $request, string $id)
    {
        $univ = Univ::findOrFail($id);

        $validated = $request->validate([
            'kode_univ' => ['required', 'string', 'max:50', Rule::unique('univs', 'kode_univ')->ignore($univ->id)],
            'nama_univ' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255'],
            'website' => ['required', 'string', 'max:255'],
        ]);

        $univ->update($validated);

        return back()->with('success', 'Universitas berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $univ = Univ::findOrFail($id);

        if ($univ->fakultas()->count() > 0) {
            return back()->with('error', 'Universitas tidak dapat dihapus karena memiliki fakultas yang terhubung.');
        }

        $univ->delete();

        return back()->with('success', 'Universitas berhasil dihapus!');
    }
}
