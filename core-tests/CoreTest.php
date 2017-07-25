<?php

use Core\Managers;
use Core\App as CoreApp;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Core\Contracts\Repositories\User as UserRepositoryContract;
use Core\Contracts\Repositories\Token as TokenRepositoryContract;

class CoreTest extends TestCase
{
    public function testCoreInstance()
    {
        $this->assertInstanceOf(CoreApp::class, app(CoreApp::class));
    }

    public function testAuthManagerInstance()
    {
        $this->assertInstanceOf(Managers\UserManager::class, app(CoreApp::class)->user());
    }

    public function testUserManagerInstance()
    {
        $this->assertInstanceOf(Managers\UserManager::class, app(CoreApp::class)->user());
    }
}
