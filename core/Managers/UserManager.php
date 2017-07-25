<?php

namespace Core\Managers;

use DateTime;
use IteratorAggregate;
use Core\PubSub\Emitter;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Core\Validator\Validator;
use Core\Transformer\Transformer;
use Core\Responses\User as UserResponse;
use Core\Validator\Rules\CreateUserRule;
use Core\Validator\Rules\UpdateUserRule;
use Core\PubSub\Publishers\UserHasBeenCreated;
use Core\Exceptions\ResourceNotFoundException;
use Core\Contracts\Util\Hasher as HasherContract;
use Core\Contracts\Repositories\User as UserRepositoryContract;

class UserManager
{
    protected $transformer;

    protected $eventEmitter;

    protected $userRepository;

    protected $hasher;

    protected $validator;

    public function __construct(
        Transformer $transformer,
        Emitter $eventEmitter,
        UserRepositoryContract $userRepository,
        HasherContract $hasher,
        Validator $validator
    ) {
        $this->transformer = $transformer;
        $this->eventEmitter = $eventEmitter;
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
        $this->validator = $validator;
    }

    public function all(): IteratorAggregate
    {
        return $this->transformer->transform(
            $this->userRepository->all()
        );
    }

    public function create(array $attributes): UserResponse
    {
        $this->validator
             ->prepare(CreateUserRule::class, $attributes)
             ->validate();

        $user = $this->userRepository->make();

        foreach ([
            'name',
            'email',
            'phone',
            'address',
            'role',
        ] as $attribute) {
            $user->set($attribute, $attributes[$attribute]);
        }

        // Store it for sending email ;)
        $password = Str::random(8);

        $user->set('password', $this->hasher->make($password));
        $user->set('created_at', new DateTime('now'));

        if ($sex = Arr::get($attributes, 'sex')) {
            $user->set('sex', $sex);
        }

        $this->eventEmitter->emit(UserHasBeenCreated::class, $user, $password);

        $this->userRepository->save($user);

        return $this->transformer->transform($user);
    }

    public function update($id, array $attributes): UserResponse
    {
        $this->validator
             ->prepare(UpdateUserRule::class, $attributes)
             ->validate();

        $user = $this->find($id);

        if ($user === null) {
            throw new ResourceNotFoundException('Resource Not Found.', 404);
        }

        foreach ([
            'name',
            'email',
            'phone',
            'address',
            'role',
        ] as $attribute) {
            $user->set($attribute, $attributes[$attribute]);
        }

        $user->set('updated_at', new DateTime('now'));

        if ($sex = Arr::get($attributes, 'sex')) {
            $user->set('sex', $sex);
        }

        $this->userRepository->save($user);

        return $this->transformer->transform($user);
    }

    public function delete($id)
    {
        $user = $this->userRepository->find($id);

        if ($user === null) {
            throw new ResourceNotFoundException('Resource Not Found.', 404);
        }

        $this->userRepository->delete($user);
    }

    public function find($id): ?UserResponse
    {
        if ($user = $this->userRepository->find($id)) {
            return $this->transformer->transform($user);
        }
    }
}
