<?php

namespace Test\Core;

use Core\Managers;
use Test\TestCase;
use Core\App as CoreApp;

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
            Managers\AuthManager::class,
            app(CoreApp::class)->auth(),
            'Implementation of '.Managers\AuthManager::class.' is not an instance of '.Managers\AuthManager::class.'.'
        );
    }

    public function testUserManagerInstance()
    {
        $this->assertInstanceOf(
            Managers\UserManager::class,
            app(CoreApp::class)->user(),
            'Implementation of '.Managers\UserManager::class.' is not an instance of '.Managers\UserManager::class.'.'
        );
    }
}
