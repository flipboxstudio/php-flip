<?php

namespace Test\Core;

use Test\TestCase;
use Core\App as CoreApp;
use Core\Responses\UserResponse;
use Core\Responses\TokenResponse;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Core\Contracts\Models\User as UserModelContract;
use Core\Contracts\Models\Token as TokenModelContract;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    protected $auth;

    public function setUp()
    {
        $this->auth = app(CoreApp::class)->auth();
    }

    public function testAuthentication()
    {
        $tokenResponse = $this->auth->authenticate('admin@core.com', 'admin');

        $this->assertInstanceOf(
            TokenResponse::class,
            $tokenResponse,
            'Implementation of '.TokenResponse::class.' is not an instance of '.TokenResponse::class.'.'
        );

        $this->assertInstanceOf(
            TokenModelContract::class,
            $tokenResponse->getToken(),
            'Implementation of '.TokenModelContract::class.' is not an instance of '.TokenModelContract::class.'.'
        );

        $this->assertInstanceOf(
            UserModelContract::class,
            $user = $tokenResponse->getUser(),
            'Implementation of '.UserModelContract::class.' is not an instance of '.UserModelContract::class.'.'
        );

        $this->assertEquals(
            $user->get('email'),
            'admin@core.com',
            'Owner of the token should be the same with credentials.'
        );
    }

    /**
     * @expectedException \Core\Exceptions\AuthenticationException
     * @expectedExceptionCode 400
     */
    public function testFailedAuthenticationWithWrongCredentials()
    {
        $this->auth->authenticate('admin@core.com', 'WrongPasswordInserted');
    }

    /**
     * @expectedException \Core\Exceptions\ValidationException
     * @expectedExceptionCode 412
     */
    public function testFailedAuthenticationWithBothInputsAreEmpty()
    {
        $this->auth->authenticate('', '');
    }

    /**
     * @expectedException \Core\Exceptions\ValidationException
     * @expectedExceptionCode 412
     */
    public function testFailedAuthenticationWithInvalidEmail()
    {
        $this->auth->authenticate('TisShouldBeAValidEmailAddresss', '');
    }

    public function testAuthorization()
    {
        $tokenResponse = $this->auth->authenticate('admin@core.com', 'admin');
        $userResponse = $this->auth->authorize(
            $tokenResponse->getToken()->get('token')
        );

        $this->assertInstanceOf(
            UserResponse::class,
            $userResponse,
            'Implementation of '.UserResponse::class.' is not an instance of '.UserResponse::class.'.'
        );

        $this->assertInstanceOf(
            UserModelContract::class,
            $userResponse->getUser(),
            'Implementation of '.UserModelContract::class.' is not an instance of '.UserModelContract::class.'.'
        );
    }

    /**
     * @expectedException \Core\Exceptions\UnauthorizedException
     * @expectedExceptionCode 401
     */
    public function testFailAuthorization()
    {
        $userResponse = $this->auth->authorize(
            'ThisShouldBeAWrongToken'
        );
    }

    /**
     * @expectedException \Core\Exceptions\UnauthorizedException
     * @expectedExceptionCode 401
     */
    public function testLogout()
    {
        $tokenResponse = $this->auth->authenticate('admin@core.com', 'admin');
        $authToken = $tokenResponse->getToken()->get('token');

        $this->auth->logout(
            $authToken
        );

        $userResponse = $this->auth->authorize(
            $authToken
        );
    }
}
