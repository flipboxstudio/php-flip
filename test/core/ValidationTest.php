<?php

namespace Test\Core;

use Test\TestCase;
use Core\Validator\Validator;
use Core\Contracts\Repositories\User as UserRepositoryContract;
use Illuminate\Contracts\Validation\Factory as ValidatorContract;

class ValidationTest extends TestCase
{
    public function testValidatorInstance()
    {
        $this->assertInstanceOf(
            Validator::class,
            $this->core->ioc()->make(Validator::class),
            'Implementation of '.Validator::class.' is not an instance of '.Validator::class.'.'
        );

        $this->assertInstanceOf(
            Validator::class,
            $this->core->ioc()->make('core.validator'),
            'Implementation of '.Validator::class.' is not an instance of '.Validator::class.'.'
        );

        $this->assertInstanceOf(
            ValidatorContract::class,
            $this->core->ioc()->make(ValidatorContract::class),
            'Implementation of '.ValidatorContract::class.' is not an instance of '.ValidatorContract::class.'.'
        );

        $this->assertInstanceOf(
            ValidatorContract::class,
            $this->core->ioc()->make('core.validator.engine'),
            'Implementation of '.ValidatorContract::class.' is not an instance of '.ValidatorContract::class.'.'
        );
    }

    public function testBasicValidation()
    {
        $validator = $this->core->ioc()->make('core.validator.engine');
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
        $this->core->ioc()->instance('path.lang', dirname(__DIR__).'/../core/resources/lang');

        $validator = $this->core->ioc()->make('core.validator.engine');

        $this->core->ioc()->make('config')->set('app.locale', 'en');

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

        $validator = $this->core->ioc()->make('core.validator.engine');
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
        $validator = $this->core->ioc()->make('core.validator.engine');

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
            ['email' => ['admin@core.com', 'alfa@flipbox.co.id']],
            ['email' => ['exists:'.UserRepositoryContract::class]]
        );

        $this->assertFalse(
            $basicValidator->fails(),
            'Validation should pass for valid input.'
        );
    }
}
