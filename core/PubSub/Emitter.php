<?php

namespace Core\PubSub;

use Closure;
use Exception;
use Sabre\Event\Emitter as SabreEventEmitter;
use Core\PubSub\Publishers\UserHasBeenCreated;
use Core\Contracts\Container as ContainerContract;
use Core\PubSub\Publishers\CustomerHasBeenRegistered;
use Core\PubSub\Publishers\CustomerForgotTheirPassword;

class Emitter
{
    protected $container;

    protected $emitter;

    protected $registered = [];

    protected $events = [
        CustomerForgotTheirPassword::class,
        CustomerHasBeenRegistered::class,
        UserHasBeenCreated::class,
    ];

    public function __construct(ContainerContract $container, SabreEventEmitter $emitter)
    {
        $this->container = $container;
        $this->emitter = $emitter;
    }

    public function emit(string $eventName, ...$arguments)
    {
        if ($this->shouldRegisterSubscriptions($eventName)) {
            $this->registerEvent($eventName);
        }

        $this->emitter->emit($eventName, $arguments);
    }

    public function subscribe(string $eventName, $handler, int $priority = 100)
    {
        $this->emitter->on($eventName, $this->factory($handler), $priority);
    }

    protected function factory($handler): Closure
    {
        // Handler is:
        // function ($foo, $bar) { doSomething($foo, $bar) }
        if ($handler instanceof Closure) {
            return $handler;
        }

        // Handler is:
        // [SomeClass::class, 'staticMethod']
        if (is_callable($handler)) {
            return function (...$arguments) use ($handler) {
                return call_user_func_array(
                    $handler,
                    $arguments
                );
            };
        }

        // Handler is:
        // Class that has:
        // - `call` method
        // - `fire` method
        // - `trigger` method
        // - `__invoke` method
        if (is_string($handler)) {
            return function (...$arguments) use ($handler) {
                return $this->triggerSubscriber($handler, $arguments);
            };
        }
    }

    protected function shouldRegisterSubscriptions(string $eventName): bool
    {
        return !in_array($eventName, $this->registered) && // the subscribers not registered yet
                in_array($eventName, $this->events); // event registered
    }

    protected function registerEvent(string $eventName)
    {
        // Insert this event name, so we know that it's subscribers has been registered
        $this->registered[] = $eventName;

        // Create event instance
        $event = new $eventName();

        // Iterate over events subscribers
        foreach ($event->subscribers() as $subscriptionName => $priority) {
            // Register event to the emitter
            $this->subscribe($eventName, $subscriptionName, $priority);
        }
    }

    protected function triggerSubscriber(string $subscriptionName, array $arguments)
    {
        // Create subscriber class
        $subscription = $this->container->make($subscriptionName);

        // Has one of these method
        foreach (['call', 'fire', 'trigger'] as $method) {
            if (method_exists($subscription, $method)) {
                return call_user_func_array(
                    [$subscription, 'call'],
                    $arguments
                );
            }
        }

        // Has `__invoke` method
        if (is_callable($subscription)) {
            return call_user_func_array($subscription, $arguments);
        }

        throw new Exception('Cannot invoke subscription.', 500);
    }
}
