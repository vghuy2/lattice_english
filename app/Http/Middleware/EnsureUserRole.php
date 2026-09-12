<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $user = Auth::user();

        if (! $user->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
            ]);
        }

        $expectedRole = UserRole::tryFrom($role);

        if ($expectedRole === UserRole::ADMIN && ! $user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Bạn không có quyền truy cập trang quản trị.'], 403);
            }
            abort(403, 'Bạn không có quyền truy cập trang quản trị.');
        }

        if ($expectedRole === UserRole::STUDENT && ! $user->isStudent()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tài khoản Quản trị viên không truy cập giao diện học viên.'], 403);
            }
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
