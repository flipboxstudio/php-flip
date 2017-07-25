<?php

namespace Core\Contracts\Transformer;

use Core\Util\Data\Fluent;

interface Autobot
{
    public function canTransform($model): bool;

    public function transform($model): Fluent;
}
