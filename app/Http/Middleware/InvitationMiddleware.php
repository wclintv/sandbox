<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Models\Invitation;

class InvitationMiddleware
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

        //check that the decrypted token matches an unique email from inviations table.
        $token = $request->encrypted_token;
        $invitation = Invitation::where('token', $token)->first();
        if( $invitation == null)
        {
            return response('Your invitation has expired, or been canceled, please ask your admin to resend it.',403);
        }
        //next, check that the invitation is less that 48 hours old.
        $updated_at = $invitation->updated_at;
        $expires = $updated_at->copy()->addHours(48);
        $now = Carbon::now();

        if($now >= $expires)
        {
            return response('Your invitation has expired, or been canceled, please ask your admin to resend it.', 403);
        }            
        return $next($request);
    
    
    }
}
