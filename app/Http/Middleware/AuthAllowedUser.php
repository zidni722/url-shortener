<?php
/**
 * User: zidni
 * Date: 2019-10-19
 * Time: 11:06
 */

namespace App\Http\Middleware;

use Closure;

class AuthAllowedUser
{
    public function handle($request, Closure $next) {
        if (!empty($request->header('x-api-key'))) {
            if ($request->header('x-api-key') != env('API_KEY')) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
