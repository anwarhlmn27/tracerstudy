<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $prodis = [];
        if ($user->role === 'alumni') {
            $prodis = \App\Models\Prodi::all();
        }

        return view('profile.edit', compact('user', 'prodis'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];

        if ($user->role === 'alumni') {
            $rules = array_merge($rules, [
                'nim' => ['required', 'string', 'max:20', Rule::unique('students', 'nim')->ignore($user->student->id ?? '')],
                'prodi_id' => ['required', 'exists:prodis,id'],
                'angkatan' => ['required', 'integer', 'min:2000', 'max:2099'],
                'status_alumni' => ['required', 'string', Rule::in(['Bekerja (full time / part time)', 'Belum memungkinkan bekerja', 'Wiraswasta', 'Melanjutkan Pendidikan', 'Tidak kerja tetapi sedang mencari kerja'])],
                'nama_perusahaan' => ['nullable', 'string', 'max:255'],
                'jabatan' => ['nullable', 'string', 'max:255'],
                'tempat_kerja' => ['nullable', 'string', Rule::in(['Lokal', 'Nasional', 'Multinasional'])],
                'waktu_tunggu_kerja' => ['nullable', 'string', 'max:255'],
                'response_rate' => ['nullable', 'integer', 'min:0', 'max:100'],
            ]);
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $file = $request->file('avatar');
            $filename = 'avatars/' . \Illuminate\Support\Str::uuid() . '.' . $file->getClientOriginalExtension();
            try {
                $this->compressImage($file, $filename, 60);
                $user->avatar = $filename;
            } catch (\Exception $e) {
                $user->avatar = $file->store('avatars', 'public');
            }
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($user->role === 'alumni') {
            if (!$user->student) {
                $user->student()->create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'nama_student' => $user->name,
                    'nim' => $validated['nim'],
                    'prodi_id' => $validated['prodi_id'],
                    'angkatan' => $validated['angkatan'],
                    'status' => 'lulus',
                    'status_alumni' => $validated['status_alumni'] ?? null,
                    'nama_perusahaan' => $validated['nama_perusahaan'] ?? null,
                    'jabatan' => $validated['jabatan'] ?? null,
                    'tempat_kerja' => $validated['tempat_kerja'] ?? null,
                    'waktu_tunggu_kerja' => $validated['waktu_tunggu_kerja'] ?? null,
                    'response_rate' => $validated['response_rate'] ?? null,
                ]);
            } else {
                $user->student->update([
                    'nim' => $validated['nim'],
                    'prodi_id' => $validated['prodi_id'],
                    'angkatan' => $validated['angkatan'],
                    'status_alumni' => $validated['status_alumni'] ?? null,
                    'nama_perusahaan' => $validated['nama_perusahaan'] ?? null,
                    'jabatan' => $validated['jabatan'] ?? null,
                    'tempat_kerja' => $validated['tempat_kerja'] ?? null,
                    'waktu_tunggu_kerja' => $validated['waktu_tunggu_kerja'] ?? null,
                    'response_rate' => $validated['response_rate'] ?? null,
                ]);
            }
        }

        if (in_array($user->role, ['alumni', 'atasan'])) {
            return redirect()->route('form.create')->with('success', 'Profil berhasil diperbarui!');
        }

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Compress uploaded image file using GD library.
     */
    private function compressImage($sourceFile, $destinationPath, $quality = 70)
    {
        $mime = $sourceFile->getMimeType();
        $tempPath = tempnam(sys_get_temp_dir(), 'avatar_');

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($sourceFile->getRealPath());
                imagejpeg($image, $tempPath, $quality);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourceFile->getRealPath());
                imagealphablending($image, false);
                imagesavealpha($image, true);
                imagepng($image, $tempPath, 6); // PNG scale is 0-9
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourceFile->getRealPath());
                imagegif($image, $tempPath);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($sourceFile->getRealPath());
                imagewebp($image, $tempPath, $quality);
                break;
            default:
                copy($sourceFile->getRealPath(), $tempPath);
                break;
        }

        if (isset($image)) {
            imagedestroy($image);
        }

        \Illuminate\Support\Facades\Storage::disk('public')->put($destinationPath, file_get_contents($tempPath));
        @unlink($tempPath);
    }
}
