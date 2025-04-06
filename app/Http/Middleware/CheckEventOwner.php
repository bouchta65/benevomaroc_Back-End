<?php

namespace App\Http\Middleware;

use App\Models\Evenement;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class CheckEventOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
{
    $event_id = $request->route('id');
    $association = Auth::user()->id;

    $event = Evenement::where('id', $event_id)
                  ->where('association_id', $association)
                  ->first();

    if (!$event) {
        return response()->json([
            'message' => 'Vous n\'êtes pas autorisé à accéder à cet événement.'
        ], 403);
    }

    return $next($request);
}
}
