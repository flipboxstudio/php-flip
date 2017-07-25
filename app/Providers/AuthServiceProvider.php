<?php

namespace App\Providers;

use Core\App as CoreApp;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
    }

    /**
     * Boot the authentication services for the application.
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            $token = $this->getApiToken($request);

            if (!$token) {
                return null;
            }

            return $this->app->make(CoreApp::class)->auth()->authorize(
                $this->getApiToken($request)
            );
        });
    }

    /**
     * Get token from request.
     *
     * @param Request $request
     *
     * @return string
     */
    protected function getApiToken(Request $request): ?string
    {
        return $request->bearerToken() ?? $request->get('api_token');
    }
}
