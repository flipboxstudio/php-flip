<?php

namespace App\Util;

use Core\Contracts\Util\Hasher as CoreHasherContract;
use Illuminate\Contracts\Hashing\Hasher as IlluminateHasherContract;

class Hasher implements CoreHasherContract
{
    protected $laravelHasher;

    public function __construct(IlluminateHasherContract $laravelHasher)
    {
        $this->laravelHasher = $laravelHasher;
    }

    public function check(string $plainPassword, string $encryptedPassword): bool
    {
        return $this->laravelHasher->check($plainPassword, $encryptedPassword);
    }

    public function make(string $plainPassword): string
    {
        return $this->laravelHasher->make($plainPassword);
    }
}
