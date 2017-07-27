<?php

namespace Core\Validator\Rules;

use Core\Contracts\Repositories\User as UserRepository;

class UpdateUserRule extends Rule
{
    public function rules(): array
    {
        return [
            'id' => [
                'required',
            ],
            'name' => [
                'required',
                'alpha',
                'max:128',
            ],
            'email' => [
                'required',
                'email',
                'max:64',
                'unique:'.UserRepository::class.',email,'.$this->getAttribute('id'),
            ],
            'phone' => [
                'required',
                'alpha_num',
                'max:16',
            ],
            'address' => [
                'required',
                'max:512',
            ],
            'role' => [
                'required',
                'in:'.implode(',', [
                    'ADM', // ADMIN
                    'USR', // USER
                ]),
            ],
            'sex' => [
                'in:'.implode(',', ['M', 'F']),
            ],
        ];
    }
}
