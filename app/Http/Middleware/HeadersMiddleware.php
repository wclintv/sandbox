<?php

namespace App\Http\Middleware;

use Closure;

class HeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //prevent options method
        if($request->getMethod() == "OPTIONS")
        {
            return response('not allowed', 405)->withHeaders(['X-Frame-Options' => 'DENY','Content-Security-Policy' => "frame-ancestors 'none'", 'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate']);
        }

        $response = $next($request);
        $response->headers->set('Access-Control-Allow-Methods','POST, GET, PUT');  //Disable all methods, except those used.
        $response->headers->set('X-Frame-Options','DENY');  //Clickjacking vulnerability fix.
        $response->headers->set('Content-Security-Policy',"frame-ancestors 'none'"); //Clickjacking fix for future compatibility.
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->Set('Pragma', 'no-cache');
        $response->headers->set('X-XSS-Protection', 1);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        return $response;
    }
}
