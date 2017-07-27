<?php

namespace Core\Contracts\Transformer;

use Core\Util\Data\Fluent;

interface Autobot
{
    public function transform($model): Fluent;
}
