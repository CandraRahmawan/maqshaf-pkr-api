<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $headers = [
            'Access-Control-Allow-Origin' => 'localhost:3500,maqshaf-web-dev.pesantrenkhoirurrooziqiin.com,maqshaf-web.pesantrenkhoirurrooziqiin.com'
        ];
        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }
        return $response;
    }
}
