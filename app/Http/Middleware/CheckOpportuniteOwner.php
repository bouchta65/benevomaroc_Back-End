<?php

namespace App\Http\Middleware;

use App\Models\Opportunite;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class CheckOpportuniteOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
{
    $opportunite_id = $request->route('id');
    $association = Auth::user()->id;

    $opportunite = Opportunite::where('id', $opportunite_id)
                  ->where('association_id', $association)
                  ->first();

    if (!$opportunite) {
        return response()->json([
            'message' => 'Vous n\'êtes pas autorisé à accéder à cet opportunite.'
        ], 403);
    }

    return $next($request);
}
}
