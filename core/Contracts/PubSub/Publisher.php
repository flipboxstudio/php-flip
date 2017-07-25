<?php

namespace Core\Contracts\PubSub;

interface Publisher
{
    public function subscribers(): array;
}
