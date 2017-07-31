<?php

namespace Test\Core;

use Test\TestCase;
use Core\PubSub\Emitter;
use Test\Core\PubSub\Publishers\TestPublisher;
use Core\Contracts\Container as ContainerContract;
use Test\Core\PubSub\Publishers\SequentialPublisher;
use Test\Core\PubSub\Subscriptions\StaticTestSubscription;
use Test\Core\PubSub\Subscriptions\InvokableTestSubscription;

class EventTest extends TestCase
{
    public function testBasic()
    {
        $emitter = $this->core->ioc()->make(Emitter::class);

        $this->assertInstanceOf(
            Emitter::class,
            $emitter,
            'Implementation of '.Emitter::class.' is not an instance of '.Emitter::class.'.'
        );

        $emitter->register(new TestPublisher());

        $emitter->emit(TestPublisher::class, $this->core->ioc());

        $this->assertEquals($this->core->ioc()->make('foo'), 'bar');
    }

    public function testSubscribeClosure()
    {
        $emitter = $this->core->ioc()->make(Emitter::class);

        $emitter->subscribe(Foo\Bar\Baz::class, function (ContainerContract $container) {
            $container->instance('foo', 'bar');
        });

        $emitter->emit(Foo\Bar\Baz::class, $this->core->ioc());

        $this->assertEquals($this->core->ioc()->make('foo'), 'bar');
    }

    public function testSubscribeStatic()
    {
        $emitter = $this->core->ioc()->make(Emitter::class);

        $emitter->subscribe(Foo\Bar\Baz::class, [StaticTestSubscription::class, 'callStatic']);

        $emitter->emit(Foo\Bar\Baz::class, $this->core->ioc());

        $this->assertEquals($this->core->ioc()->make('foo'), 'bar');
    }

    public function testSubscribeInvoke()
    {
        $emitter = $this->core->ioc()->make(Emitter::class);

        $emitter->subscribe(Foo\Bar\Baz::class, InvokableTestSubscription::class);

        $emitter->emit(Foo\Bar\Baz::class, $this->core->ioc());

        $this->assertEquals($this->core->ioc()->make('foo'), 'bar');
    }

    public function testPriority()
    {
        $emitter = $this->core->ioc()->make(Emitter::class);

        $emitter->register(new SequentialPublisher());

        $emitter->emit(SequentialPublisher::class, $this->core->ioc());

        $this->assertEquals($this->core->ioc()->make('number'), [
            1 => 1,
            2 => 2,
            3 => 3,
        ]);
    }
}
