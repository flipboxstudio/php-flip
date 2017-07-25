<?php

namespace App\Providers;

use App\Application;
use App\Util\Hasher;
use Core\App as CoreApp;
use App\Infrastructure\Mailer;
use App\Repositories\UserRepository;
use App\Repositories\TokenRepository;
use Illuminate\Support\ServiceProvider;
use Core\Contracts\Util\Hasher as HasherContract;
use Core\Contracts\Container as CoreContainerContract;
use Core\Contracts\Infrastructure\Mailer as MailerContract;
use Core\Contracts\Repositories\User as UserRepositoryContract;
use Core\Contracts\Repositories\Token as TokenRepositoryContract;
use Illuminate\Contracts\Hashing\Hasher as IlluminateHasherContract;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(CoreApp::class, function ($app) {
            return CoreApp::make(function (CoreContainerContract $coreContainer) use ($app) {
                $coreContainer->singleton(UserRepositoryContract::class, UserRepository::class);
                $coreContainer->singleton(TokenRepositoryContract::class, TokenRepository::class);

                $coreContainer->instance(IlluminateHasherContract::class, $app->make('hash'));
                $coreContainer->singleton(HasherContract::class, Hasher::class);

                $coreContainer->singleton(MailerContract::class, Mailer::class);

                return $coreContainer;
            });
        });
    }
}
