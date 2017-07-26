<?php

namespace Core\Validator\Rules;

use Core\Contracts\Repositories\User as UserRepository;

class UserRegistrationRule extends Rule
{
    public function rules(): array
    {
        return [
            'name' => [
                'notEmpty',
                'alpha',
                'length(null,128)',
            ],
            'email' => [
                'notEmpty',
                'email',
                'max_length(64)',
                'unique(email)' => $this->generateUniqueValidation(
                    $this->container->make(UserRepository::class)
                ),
            ],
            'phone' => [
                'notEmpty',
                'alpha_numeric',
                'max_length(16)',
            ],
            'address' => [
                'notEmpty',
                'max_length(512)',
            ],
            'password' => [
                'notEmpty',
                'min_length(8)',
                'equals(:password_verify)',
            ],
            'password_verify' => [
                'notEmpty',
                'min_length(8)',
            ],
            'sex' => [
                'in' => $this->generateInValidation(['M', 'F']),
            ],
        ];
    }
}
