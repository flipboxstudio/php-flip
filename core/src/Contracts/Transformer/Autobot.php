<?php

namespace Core\Contracts\Transformer;

use Core\Util\Data\Fluent;
use Core\Contracts\Models\Model as ModelContract;

interface Autobot
{
    public function transform(ModelContract $model): Fluent;
}
