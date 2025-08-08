<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSubdomainRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->headers->has("iam-trust")) {
            return response()->json([
                "message" => "Acceso no autorizado",
            ], 401);
        }

        $request_key = $request->header("iam-trust");
        $api_trust_key = "dG9kb0VzdGFyYU11eUJpZW4=";

        if ($request_key !== $api_trust_key) {
            return response()->json([
                "message" => "Acceso no autorizado",
            ], 401);
        }

        return $next($request);
    }
}
