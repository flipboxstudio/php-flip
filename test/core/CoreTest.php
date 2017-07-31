<?php

namespace Test\Core;

use Test\TestCase;
use Core\App as CoreApp;
use Core\Managers\AuthManager;
use Core\Managers\UserManager;

class CoreTest extends TestCase
{
    public function testCoreInstance()
    {
        $this->assertInstanceOf(
            CoreApp::class,
            app(CoreApp::class),
            'Implementation of '.CoreApp::class.' is not an instance of '.CoreApp::class.'.'
        );
    }

    public function testCoreInstanceUsingAlias()
    {
        $this->assertInstanceOf(
            CoreApp::class,
            app('core'),
            'Implementation of '.CoreApp::class.' is not an instance of '.CoreApp::class.'.'
        );
    }

    public function testAuthManagerInstance()
    {
        $this->assertInstanceOf(
            AuthManager::class,
            app(CoreApp::class)->auth(),
            'Implementation of '.AuthManager::class.' is not an instance of '.AuthManager::class.'.'
        );
    }

    public function testAuthManagerInstanceUsingCoreContainer()
    {
        $this->assertInstanceOf(
            AuthManager::class,
            app('core')->ioc()->make(AuthManager::class),
            'Implementation of '.AuthManager::class.' is not an instance of '.AuthManager::class.'.'
        );

        $this->assertInstanceOf(
            AuthManager::class,
            app('core')->ioc(AuthManager::class),
            'Implementation of '.AuthManager::class.' is not an instance of '.AuthManager::class.'.'
        );
    }

    public function testAuthManagerInstanceUsingCoreContainerViaAlias()
    {
        $this->assertInstanceOf(
            AuthManager::class,
            app('core')->ioc()->make('core.manager.auth'),
            'Implementation of '.AuthManager::class.' is not an instance of '.AuthManager::class.'.'
        );

        $this->assertInstanceOf(
            AuthManager::class,
            app('core')->ioc('core.manager.auth'),
            'Implementation of '.AuthManager::class.' is not an instance of '.AuthManager::class.'.'
        );
    }

    public function testUserManagerInstance()
    {
        $this->assertInstanceOf(
            UserManager::class,
            app(CoreApp::class)->user(),
            'Implementation of '.UserManager::class.' is not an instance of '.UserManager::class.'.'
        );
    }

    public function testUserManagerInstanceUsingCoreContainer()
    {
        $this->assertInstanceOf(
            UserManager::class,
            app('core')->ioc()->make(UserManager::class),
            'Implementation of '.UserManager::class.' is not an instance of '.UserManager::class.'.'
        );

        $this->assertInstanceOf(
            UserManager::class,
            app('core')->ioc(UserManager::class),
            'Implementation of '.UserManager::class.' is not an instance of '.UserManager::class.'.'
        );
    }

    public function testUserManagerInstanceUsingCoreContainerViaAlias()
    {
        $this->assertInstanceOf(
            UserManager::class,
            app('core')->ioc()->make('core.manager.user'),
            'Implementation of '.UserManager::class.' is not an instance of '.UserManager::class.'.'
        );

        $this->assertInstanceOf(
            UserManager::class,
            app('core')->ioc('core.manager.user'),
            'Implementation of '.UserManager::class.' is not an instance of '.UserManager::class.'.'
        );
    }
}
