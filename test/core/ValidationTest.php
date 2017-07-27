<?php

namespace Test\Core;

use Test\TestCase;
use Core\App as CoreApp;
use Illuminate\Contracts\Validation\Factory as Validator;
use Core\Contracts\Repositories\User as UserRepositoryContract;

class ValidationTest extends TestCase
{
    public function testValidatorInstance()
    {
        $validator = app(CoreApp::class)->ioc()->make(Validator::class);

        $this->assertInstanceOf(
            Validator::class,
            $validator,
            'Implementation of '.Validator::class.' is not an instance of '.Validator::class.'.'
        );
    }

    public function testBasicValidation()
    {
        $validator = app(CoreApp::class)->ioc()->make(Validator::class);
        $basicValidator = $validator->make(
            ['email' => 'krisan47@gmail.com'],
            ['email' => ['required', 'email']]
        );

        $this->assertFalse(
            $basicValidator->fails(),
            'Validation should not fail for valid input.'
        );

        $basicValidator = $validator->make(
            ['email' => 'anu'],
            ['email' => ['required', 'email']]
        );

        $this->assertTrue(
            $basicValidator->fails(),
            'Validation should fail for invalid input.'
        );

        $basicValidator = $validator->make(
            ['email' => ''],
            ['email' => ['required', 'email']]
        );

        $this->assertTrue(
            $basicValidator->fails(),
            'Validation should fail for invalid input.'
        );
    }

    public function testErrorMessage()
    {
        app(CoreApp::class)->ioc()->instance('path.lang', dirname(__DIR__).'/../core/resources/lang');

        $validator = app(CoreApp::class)->ioc()->make(Validator::class);

        app(CoreApp::class)->ioc()->make('config')->set('app.locale', 'en');

        $basicValidator = $validator->make(
            ['email' => ''],
            ['email' => ['required', 'email']]
        );

        $errors = $basicValidator->errors();

        $this->assertTrue(
            $basicValidator->fails(),
            'Validation should fail for invalid input.'
        );

        $this->assertEquals(
            $errors->first('email'),
            'The email field is required.'
        );

        $validator = app(CoreApp::class)->ioc()->make(Validator::class);
        $basicValidator = $validator->make(
            ['email' => 'anu'],
            ['email' => ['required', 'email']]
        );

        $errors = $basicValidator->errors();

        $this->assertTrue(
            $basicValidator->fails(),
            'Validation should fail for invalid input.'
        );

        $this->assertEquals(
            $errors->first('email'),
            'The email must be a valid email address.'
        );
    }

    public function testDatabaseValidation()
    {
        $validator = app(CoreApp::class)->ioc()->make(Validator::class);

        $basicValidator = $validator->make(
            ['email' => 'admin@core.com'],
            ['email' => ['required', 'email', 'unique:'.UserRepositoryContract::class.',email,1']]
        );

        $this->assertFalse(
            $basicValidator->fails(),
            'Validation should pass for valid input.'
        );

        $basicValidator = $validator->make(
            ['email' => 'admin@core.com'],
            ['email' => ['unique:'.UserRepositoryContract::class]]
        );

        $this->assertTrue(
            $basicValidator->fails(),
            'Validation should fail for invalid input.'
        );

        $basicValidator = $validator->make(
            ['email' => ['admin@core.com']],
            ['email' => ['exists:'.UserRepositoryContract::class]]
        );

        $this->assertFalse(
            $basicValidator->fails(),
            'Validation should pass for valid input.'
        );
    }
}
