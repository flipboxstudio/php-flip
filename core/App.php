<?php

namespace Core;

use Closure;
use RuntimeException;
use Core\PubSub\Emitter;
use Core\Managers\AuthManager;
use Core\Managers\UserManager;
use Core\Contracts\Util\Hasher as HasherContract;
use Core\Contracts\Container as ContainerContract;
use Core\Contracts\Infrastructure\Mailer as MailerContract;
use Core\Contracts\Repositories\User as UserRepositoryContract;
use Core\Contracts\Repositories\Token as TokenRepositoryContract;

/**
 * Factory class.
 *
 * This class is responsible to create manager.
 * You SHOULD NOT instantiate the manager manualy, it will break somewhere.
 */
class App
{
    protected $container;

    protected $resolved = [];

    protected $requiredBindings = [
        HasherContract::class,
        MailerContract::class,
        UserRepositoryContract::class,
        TokenRepositoryContract::class,
    ];

    /**
     * Direct instantiation is not recommended. Use static::make method otherwise.
     *
     * @param ContainerContract $container
     */
    public function __construct(ContainerContract $container)
    {
        $this->container = $container;

        foreach ($this->requiredBindings as $requiredBinding) {
            if (!$this->container->bound($requiredBinding)) {
                throw new RuntimeException("{$requiredBinding} is not bound to the Container.");
            }
        }

        $this->container->instance(ContainerContract::class, $container);
        $this->container->singleton(Emitter::class, Emitter::class);
        $this->container->instance(self::class, $this);
    }

    public static function make(Closure $callback, ContainerContract $container = null): App
    {
        return new self(static::createContainer(
            $callback,
            $container
        ));
    }

    public function auth(): AuthManager
    {
        return $this->create(AuthManager::class);
    }

    public function user(): UserManager
    {
        return $this->create(UserManager::class);
    }

    protected function create(string $className)
    {
        // Caching strategy
        if (array_key_exists($className, $this->resolved)) {
            return $this->resolved[$className];
        }

        return $this->resolved[$className] = $this->container->make($className);
    }

    protected static function createContainer(Closure $callback, ContainerContract $container = null): ContainerContract
    {
        return call_user_func_array($callback, [
            $container = ($container === null) ? new Container() : $container,
        ]);
    }
}
