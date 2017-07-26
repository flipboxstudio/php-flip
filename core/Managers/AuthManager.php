<?php

namespace Core\Managers;

use DateTime;
use Core\PubSub\Emitter;
use Illuminate\Support\Arr;
use Core\Validator\Validator;
use Core\Concerns\Authorizable;
use Core\Responses\UserResponse;
use Core\Transformer\Transformer;
use Core\Concerns\Authenticatable;
use Core\Contracts\Util\Hasher as HasherContract;
use Core\Validator\Rules\CustomerRegistrationRule;
use Core\Contracts\Repositories\User as UserRepositoryContract;
use Core\Contracts\Repositories\Token as TokenRepositoryContract;

class AuthManager
{
    use Authenticatable, Authorizable;

    protected $userRepository;

    protected $tokenRepository;

    protected $validator;

    protected $transformer;

    protected $emitter;

    protected $hasher;

    public function __construct(
        UserRepositoryContract $userRepository,
        TokenRepositoryContract $tokenRepository,
        Validator $validator,
        Transformer $transformer,
        Emitter $emitter,
        HasherContract $hasher
    ) {
        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
        $this->validator = $validator;
        $this->transformer = $transformer;
        $this->emitter = $emitter;
        $this->hasher = $hasher;
    }

    /**
     * Register a customer.
     *
     * @param array $attributes
     *
     * @return UserResponse
     *
     * @throws \Core\Exceptions\ValidationException
     */
    public function registerCustomer(array $attributes): UserResponse
    {
        $this->validator
             ->prepare(CustomerRegistrationRule::class, $attributes)
             ->validate();

        $user = $this->userRepository->make();

        $user->set('name', $attributes['name']);
        $user->set('email', $attributes['email']);
        $user->set('phone', $attributes['phone']);

        $user->set('password', $this->hasher->make($attributes['password']));
        $user->set('role', 'CST');

        if ($sex = Arr::get($attributes, 'sex')) {
            $user->set('sex', $sex);
        }

        $user->set('created_at', new DateTime('now'));

        $this->emitter->emit(CustomerHasBeenRegistered::class, $user);

        $this->userRepository->save($user);

        return $this->transformer->transform($user);
    }
}
