<?php

namespace Core\Util\Presenters;

use Core\Contracts\Util\Presenter as PresenterContract;

class JSON implements PresenterContract
{
    public function present($data)
    {
        return json_encode($data);
    }
}
