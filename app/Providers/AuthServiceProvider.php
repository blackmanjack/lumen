<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                $token = explode(' ', $request->header('Authorization'));
                if($token[0] === 'Basic'){
                    $split = explode(':',base64_decode($token[1]));
                    $username = $split[0];
                    $password = hash('sha256', $split[1]);

                    return User::where('username', $username)
                    ->where('password', $password)
                    ->first();
                }
                return;
            }
        });
    }
}
