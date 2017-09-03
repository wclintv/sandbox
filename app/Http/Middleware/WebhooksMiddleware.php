<?php

namespace App\Http\Middleware;

use Closure;
use App\Plugins\QuickbooksOnline\QuickbooksOnline;
use Illuminate\Http\Response;
use App\Utility\Qlog;

class WebhooksMiddleware
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
        $payload = $request->getContent();
        $signature = $request->header('intuit-signature');

        if(QuickbooksOnline::webhook_verify($payload, $signature))
        {
            return $next($request);
        }
        return response('Intuit webhook verification failed.',401);        
    }
}
