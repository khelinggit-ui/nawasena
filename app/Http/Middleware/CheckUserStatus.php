<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Periksa jika user sudah authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Jika status user adalah Inactive, logout dan redirect ke login dengan pesan error
            if ($user->status === 'Inactive') {
                Auth::logout();
                
                return redirect()->route('login')
                    ->with('error', 'Your account has been deactivated. Please contact administrator.');
            }
        }

        return $next($request);
    }
}
