<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->header('Authorization')) {
            $token = explode(' ', $request->header('Authorization'));
            if($token[0] === 'Basic'){
                $split = explode(':',base64_decode($token[1]));
                // Get the username from the request (adjust this based on your request data)
                $username = $split[0];
                // Perform the status check
                $statusCheck = User::where('username', $username)->value('status');

                if ($statusCheck === false) {
                    $message = "Your account is inactive. Check your email for activation";
                    return response()->json(['error' => $message], 403);
                }
            }
        }

        if ($this->auth->guard($guard)->guest()) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
