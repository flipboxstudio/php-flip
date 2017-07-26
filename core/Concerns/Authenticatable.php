<?php

namespace Core\Concerns;

use DateTime;
use DateInterval;
use Illuminate\Support\Str;
use Core\Responses\User as UserResponse;
use Core\Responses\Token as TokenResponse;
use Core\Exceptions\AuthenticationException;
use Core\PubSub\Publishers\CustomerForgotTheirPassword;

trait Authenticatable
{
    public function authenticate(string $email, string $password): TokenResponse
    {
        $user = $this->userRepository->findBy('email', $email);

        if ($user !== null) {
            if ($this->hasher->check($password, $user->get('password'))) {
                $token = $this->tokenRepository->makeFromUser($user);

                $token->set('token', Str::random(64));
                $token->set('expired_at', $this->generateExpirationDate());
                $token->set('created_at', new DateTime('now'));

                $this->tokenRepository->save($token);

                return $this->transformer->transform($token);
            }
        }

        throw new AuthenticationException('Incorrect Email and / or Password.', 400);
    }

    public function logout(string $token)
    {
        $this->tokenRepository->delete(
            $this->tokenRepository->findBy('token', $token)
        );
    }

    public function forgot(string $email): UserResponse
    {
        $user = $this->userRepository->findBy('email', $email);

        if ($user === null) {
            throw new AuthenticationException('That email is not registered in our system.', 400);
        }

        // Generate token if it's not generated yet,
        // otherwise, old token will sent to email.
        $rememberToken = $user->get('remember_token') ?: Str::random(64);

        $user->set('remember_token', $rememberToken);

        $this->eventEmitter->emit(CustomerForgotTheirPassword::class, $user);

        $this->userRepository->save($user);

        return $this->transformer->transform($user);
    }

    protected function generateExpirationDate()
    {
        $date = new DateTime('now');

        $date->add(new DateInterval('P7D')); // Plus 7 Days

        return $date;
    }
}
