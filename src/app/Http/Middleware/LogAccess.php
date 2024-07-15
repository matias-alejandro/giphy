<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AccessLog;

class LogAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('OPTIONS')) {
            return $response;
        }

        try {
            AccessLog::create([
                'user_id' => auth()->user()->id ?? null,
                'service' => $request->path(),
                'request_body' => json_encode($request->all()),
                'response_status_code' => $response->getStatusCode(),
                'response_body' => $response->getContent(),
                'origin_ip' => $request->ip(),
            ]);

            return $response;
        }
        catch(\Throwable $th) {
            return $response;
        }
    }
}
