<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Is_Perangkat
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       if(auth()->guard()->user()->role === 'perangkat'){
            return $next($request);
       }

       return redirect()->route('dashboard')->with([
            'notif_status' => 'error',
            'notif_message' => 'Anda tidak memiliki akses!!!.',
        ]);
    }
}
