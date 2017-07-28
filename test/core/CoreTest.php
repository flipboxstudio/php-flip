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

    public function testAuthManagerInstance()
    {
        $this->assertInstanceOf(
            AuthManager::class,
            app(CoreApp::class)->auth(),
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
}
