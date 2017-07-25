<?php

namespace Core\Util\Data;

use Core\Concerns\Presentable;
use Illuminate\Support\Collection as IlluminateCollection;

class Collection extends IlluminateCollection
{
    use Presentable;
}
