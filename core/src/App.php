<?php

namespace Core;

use Closure;
use RuntimeException;
use Core\PubSub\Emitter;
use Core\Validator\Validator;
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
    /**
     * Container instance.
     *
     * @var ContainerContract
     */
    protected $container;

    /**
     * Resolved instances.
     *
     * @var array
     */
    protected $resolved = [];

    /**
     * Required bindings.
     *
     * @var array
     */
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

        $this->checkRequiredBindings();

        $this->container->instance(ContainerContract::class, $container);
        $this->container->instance(self::class, $this);

        $this->container->singleton(AuthManager::class, AuthManager::class);
        $this->container->singleton(UserManager::class, UserManager::class);
        $this->container->singleton(Validator::class, Validator::class);
        $this->container->singleton(Emitter::class, Emitter::class);
    }

    /**
     * Make new instance using container modifier.
     *
     * @param Closure           $callback
     * @param ContainerContract $container
     *
     * @return App
     */
    public static function make(Closure $callback, ContainerContract $container = null): App
    {
        return new self(static::createContainer(
            $callback,
            $container
        ));
    }

    /**
     * Return the container instance.
     *
     * @return ContainerContract
     */
    public function ioc(): ContainerContract
    {
        return $this->container;
    }

    /**
     * Return the Auth Manager.
     *
     * @return AuthManager
     */
    public function auth(): AuthManager
    {
        return $this->create(AuthManager::class);
    }

    /**
     * Return the User Manager.
     *
     * @return UserManager
     */
    public function user(): UserManager
    {
        return $this->create(UserManager::class);
    }

    /**
     * Build an instance.
     *
     * @param string $className
     */
    protected function create(string $className)
    {
        // Caching strategy
        if (array_key_exists($className, $this->resolved)) {
            return $this->resolved[$className];
        }

        return $this->resolved[$className] = $this->container->make($className);
    }

    /**
     * Create container using callback.
     *
     * @param Closure           $callback
     * @param ContainerContract $container
     *
     * @return ContainerContract
     */
    protected static function createContainer(Closure $callback, ContainerContract $container = null): ContainerContract
    {
        return call_user_func_array($callback, [
            $container = ($container === null) ? new Container() : $container,
        ]);
    }

    /**
     * Check required bindings.
     */
    protected function checkRequiredBindings()
    {
        foreach ($this->requiredBindings as $requiredBinding) {
            if (!$this->container->bound($requiredBinding)) {
                throw new RuntimeException("{$requiredBinding} is not bound to the Container.");
            }
        }
    }
}
