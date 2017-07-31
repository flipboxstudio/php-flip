<?php

namespace Core\Contracts\Transformer;

use Core\Util\Data\Fluent;
use Core\Contracts\Models\Model as ModelContract;

interface Autobot
{
    public function bind(ModelContract $model): Autobot;

    public function transform(): Fluent;
}
