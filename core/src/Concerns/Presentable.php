<?php

namespace Core\Concerns;

use LogicException;

trait Presentable
{
    protected $Presenter;

    public function as(string $Presenter)
    {
        $this->Presenter = $Presenter;

        return $this;
    }

    public function present()
    {
        if (!$this->Presenter) {
            throw new LogicException('Presenter not set, use `as` method.', 500);
        }

        $presenter = new $this->Presenter();

        return $presenter->present($this);
    }
}
