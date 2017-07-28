<?php

namespace Core\Concerns;

use LogicException;

trait Presentable
{
    protected $Presenter;

    public function using(string $Presenter)
    {
        $this->Presenter = $Presenter;

        return $this;
    }

    public function present()
    {
        if (!$this->Presenter) {
            throw new LogicException('Presenter not set, use `using` method.', 500);
        }

        $presenter = new $this->Presenter();

        return $presenter->present($this);
    }
}
