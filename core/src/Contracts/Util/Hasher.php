<?php

namespace Core\Contracts\Util;

interface Hasher
{
    public function make(string $plainPassword): string;

    public function check(string $plainPassword, string $encryptedPassword): bool;
}
