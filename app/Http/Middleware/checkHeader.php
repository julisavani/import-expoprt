<?php

namespace App\Http\Middleware;

use App\Models\Merchant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('token');
        if(!isset($header)){
            return response()->json(['success' => false, 'error' => ['message' => 'Unauthorized.']], 401);
        }
        // dd($header);

        $token = Merchant::where('token', $header)->where('status', 0)->first();
        if(!$token){
            return response()->json(['success' => false, 'error' => ['message' => 'Unauthorized.']], 401);
        }

        return $next($request);
    }
}
