<?php

namespace App\Http\Middleware;

use Closure;
use Log;
class XmlEscape
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
        $data = $request->getContent();
        if(strpos($data, '<?xml') === 0)
        {
            return response('snapdsk does not accept xml', 422);
        }        
        return $next($request);
    }
}
