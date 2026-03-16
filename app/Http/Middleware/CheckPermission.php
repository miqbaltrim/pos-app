<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        try {
            $user = $request->user();

            // 1. Belum login
            if (!$user) {
                return $request->expectsJson()
                    ? response()->json(['message' => 'Unauthenticated.'], 401)
                    : redirect()->guest(route('login'));
            }

            // 2. Akun nonaktif
            if (!$user->is_active) {
                Auth::guard('web')->logout();

                if ($request->hasSession()) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                return $request->expectsJson()
                    ? response()->json(['message' => 'Akun dinonaktifkan.'], 403)
                    : redirect()->route('login')
                        ->with('error', 'Akun Anda telah dinonaktifkan.');
            }

            // 3. Super admin bypass
            if ($user->hasRole('super-admin')) {
                return $next($request);
            }

            // 4. Cek permission
            if (!$user->hasPermissionTo($permission)) {
                return $request->expectsJson()
                    ? response()->json(['message' => 'Akses ditolak.'], 403)
                    : response()->view('errors.403', [
                        'message' => 'Anda tidak memiliki akses.'
                    ], 403);
            }

            return $next($request);

        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            Log::error("Permission tidak ditemukan: {$permission}");

            return $request->expectsJson()
                ? response()->json(['message' => "Permission '{$permission}' belum terdaftar."], 500)
                : response()->view('errors.500', [
                    'message' => "Permission '{$permission}' belum terdaftar. Jalankan seeder."
                ], 500);

        } catch (\Exception $e) {
            Log::error('CheckPermission error: ' . $e->getMessage());

            return $request->expectsJson()
                ? response()->json(['message' => 'Server error.'], 500)
                : response()->view('errors.500', [
                    'message' => 'Terjadi kesalahan sistem.'
                ], 500);
        }
    }
}