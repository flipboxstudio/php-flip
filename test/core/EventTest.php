<?php

namespace Test\Core;

use Test\TestCase;
use Core\PubSub\Emitter;
use Core\App as CoreApp;
use Test\Core\PubSub\Publishers\TestPublisher;
use Core\Contracts\Container as ContainerContract;
use Test\Core\PubSub\Publishers\SequentialPublisher;
use Test\Core\PubSub\Subscriptions\StaticTestSubscription;
use Test\Core\PubSub\Subscriptions\AnotherTestSubscription;

class EventTest extends TestCase
{
    public function testBasic()
    {
        $emitter = app(CoreApp::class)->ioc()->make(Emitter::class);

        $this->assertInstanceOf(
            Emitter::class,
            $emitter,
            'Implementation of '.Emitter::class.' is not an instance of '.Emitter::class.'.'
        );

        $emitter->register(new TestPublisher());

        $emitter->emit(TestPublisher::class, app(CoreApp::class)->ioc());

        $this->assertEquals(app(CoreApp::class)->ioc()->make('foo'), 'bar');
    }

    public function testSubscribeClosure()
    {
        $emitter = app(CoreApp::class)->ioc()->make(Emitter::class);

        $emitter->subscribe(Something\Random\Event::class, function (ContainerContract $container) {
            $container->instance('foo', 'bar');
        });

        $emitter->emit(Something\Random\Event::class, app(CoreApp::class)->ioc());

        $this->assertEquals(app(CoreApp::class)->ioc()->make('foo'), 'bar');
    }

    public function testSubscribeStatic()
    {
        $emitter = app(CoreApp::class)->ioc()->make(Emitter::class);

        $emitter->subscribe(Something\Random\Event::class, [StaticTestSubscription::class, 'callStatic']);

        $emitter->emit(Something\Random\Event::class, app(CoreApp::class)->ioc());

        $this->assertEquals(app(CoreApp::class)->ioc()->make('foo'), 'bar');
    }

    public function testSubscribeInvoke()
    {
        $emitter = app(CoreApp::class)->ioc()->make(Emitter::class);

        $emitter->subscribe(Something\Random\Event::class, AnotherTestSubscription::class);

        $emitter->emit(Something\Random\Event::class, app(CoreApp::class)->ioc());

        $this->assertEquals(app(CoreApp::class)->ioc()->make('foo'), 'bar');
    }

    public function testPriority()
    {
        $emitter = app(CoreApp::class)->ioc()->make(Emitter::class);

        $emitter->register(new SequentialPublisher());

        $emitter->emit(SequentialPublisher::class, app(CoreApp::class)->ioc());

        $this->assertEquals(app(CoreApp::class)->ioc()->make('number'), [
            1 => 1,
            2 => 2,
            3 => 3,
        ]);
    }
}
