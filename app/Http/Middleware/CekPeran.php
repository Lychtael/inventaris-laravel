<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Import Auth
use Symfony\Component\HttpFoundation\Response;

class CekPeran
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $id_peran  // <-- Kita akan menerima parameter ID Peran
     */
    public function handle(Request $request, Closure $next, string $id_peran): Response
    {
        // 1. Cek jika pengguna sudah login DAN id_peran-nya sama dengan yang kita minta
        if (Auth::user() && Auth::user()->id_peran == $id_peran) {
            // 2. Jika sesuai, lanjutkan ke halaman yang dituju
            return $next($request);
        }

        // 3. Jika tidak sesuai, kembalikan ke dashboard dengan pesan error
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk halaman tersebut.');
    }
}