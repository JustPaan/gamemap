<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OrganizerApproved
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role === 'organizer') {
            if (auth()->user()->is_approved === false) {
                return redirect()->route('home')->with('error', 'Your organizer application was rejected.');
            }
            
            if (auth()->user()->is_approved === null) {
                return redirect()->route('home')->with('info', 'Your organizer application is pending approval.');
            }
        }
        
        return $next($request);
    }
}