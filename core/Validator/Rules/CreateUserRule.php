<?php

namespace Core\Validator\Rules;

use Core\Contracts\Repositories\User as UserRepository;

class CreateUserRule extends Rule
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'alpha',
                'max_length(128)',
            ],
            'email' => [
                'required',
                'email',
                'max_length(64)',
                'unique(email)' => $this->generateUniqueValidation(
                    $this->container->make(UserRepository::class)
                ),
            ],
            'phone' => [
                'required',
                'alpha_numeric',
                'max_length(16)',
            ],
            'address' => [
                'required',
                'max_length(512)',
            ],
            'role' => [
                'required',
                'in' => $this->generateInValidation([
                    'ADM', // ADMIN
                    'USR', // USER
                ]),
            ],
            'sex' => [
                'in' => $this->generateInValidation(['M', 'F']),
            ],
        ];
    }
}
