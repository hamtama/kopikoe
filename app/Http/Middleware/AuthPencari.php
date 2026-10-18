<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthPencari
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Izinkan jika kategori user adalah Pencari (ID 2) atau dia adalah Admin
        if ($user && ((int) $user->user_category_id === 2 || $user->is_admin)) {
            return $next($request);
        }

        session()->flash('error', 'Halaman ini khusus untuk Pencari Kopi / Pembeli.');
        return redirect()->route('home');
    }
}
