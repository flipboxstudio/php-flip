<?php

namespace Core\Managers;

use DateTime;
use Core\PubSub\Emitter;
use Illuminate\Support\Arr;
use Core\Validator\Validator;
use Core\Concerns\Authorizable;
use Core\Transformer\Transformer;
use Core\Concerns\Authenticatable;
use Core\Responses\User as UserResponse;
use Core\Contracts\Util\Hasher as HasherContract;
use Core\Validator\Rules\CustomerRegistrationRule;
use Core\Contracts\Repositories\User as UserRepositoryContract;
use Core\Contracts\Repositories\Token as TokenRepositoryContract;

class AuthManager
{
    use Authenticatable, Authorizable;

    protected $transformer;

    protected $emitter;

    protected $hasher;

    protected $userRepository;

    protected $tokenRepository;

    protected $validator;

    public function __construct(
        Transformer $transformer,
        Emitter $emitter,
        HasherContract $hasher,
        UserRepositoryContract $userRepository,
        Validator $validator,
        TokenRepositoryContract $tokenRepository
    ) {
        $this->transformer = $transformer;
        $this->emitter = $emitter;
        $this->hasher = $hasher;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->tokenRepository = $tokenRepository;
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
