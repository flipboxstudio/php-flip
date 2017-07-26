<?php

namespace Core\Concerns;

use Core\Responses\UserResponse;
use Core\Exceptions\UnauthorizedException;

trait Authorizable
{
    public function authorize(string $token, string $role = null): ?UserResponse
    {
        $token = $this->tokenRepository->findBy('token', $token);

        if ($token !== null) {
            $user = $token->getUser();

            if ($role) {
                $user = ($user->getRole() === $role) ? $user : null;
            }

            if ($user) {
                return $this->transformer->transform($user);
            }
        }

        throw new UnauthorizedException('Unauthorized.', 401);
    }
}
