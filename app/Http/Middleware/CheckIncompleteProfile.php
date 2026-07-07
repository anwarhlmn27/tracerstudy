<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIncompleteProfile
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only check for alumni users
        if ($user && $user->role === 'alumni') {
            $student = $user->student;
            $isIncomplete = !$student || 
                            empty($student->nim) || 
                            empty($student->prodi_id) || 
                            empty($student->angkatan) || 
                            empty($student->status_alumni);

            if ($isIncomplete) {
                // Allow access to profile edit, update, and logout to avoid redirect loop
                $allowedRoutes = ['profile.edit', 'profile.update', 'logout'];
                
                if ($request->route() && !in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('profile.edit')->with('warning', 'Harap lengkapi data diri Anda (NIM, Program Studi, Angkatan, dan Status Alumni) terlebih dahulu sebelum mengisi form.');
                }
            }
        }

        return $next($request);
    }
}
