<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


/**
 * This class provides functionality to check if the authenticated user has the ROLE_ADMIN role.
 * If the user has the ROLE_ADMIN role, they will be redirected to a route indicating they don't have access.
 */
class CheckUserRole
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $requiredAuthority)
    {
        // Check if the authenticated user has the ROLE_ADMIN role
        if (auth()->check() && !$this->hasAnyAuthority(auth()->user(), $requiredAuthority)) {
            // If the user does not have the ROLE_ADMIN role, abort with a 403 Forbidden response
            abort(403, 'Forbidden');
            // Alternatively, you can redirect to a route indicating no access
            // return redirect()->route('no-access');
        }

        return $next($request);
    }

    /**
     * Check if the user has any of the specified authorities.
     *
     * @param  mixed  $user
     * @param  string|array  $authorities
     * @return bool
     */
    public function hasAnyAuthority($user, $authorities)
    {
        // Convert a single authority string into an array for consistency
        if (is_string($authorities)) {
            $authorities = [$authorities];
        }

        // Decode the user's authorities from JSON format
        $userAuthorities = json_decode($user->kc_authorities);

        // Check if $userAuthorities is an array and not null
        if (!is_array($userAuthorities)) {
            Log::error('Invalid user authorities format: ' . json_encode($user->kc_authorities));
            return false;
        }

        // Check if ROLE_ADMIN is included in the user's authorities
        if (in_array(self::ROLE_ADMIN, $userAuthorities)) {
            Log::debug('User has ADMIN role');
            return true;
        }

        // Check if any of the specified authorities are present in the user's authorities
        // array_diff() returns an empty array if all elements in $authorities have corresponding elements in $userAuthorities
        $res = empty(array_diff($authorities, $userAuthorities));
        Log::debug(
            'Authorities :authorities are :not present in user authorities :user_authorities',
            [
            'authorities' => json_encode($authorities) ,
            'user_authorities' => json_encode($userAuthorities),
             'not' => $res ? '' : 'not'
            ]
        );
        return $res;
    }
}
