<?php

namespace Core\Util\Presenters;

use Core\Contracts\Util\Presenter as PresenterContract;

class JSONPresenter implements PresenterContract
{
    public function present($data)
    {
        return json_encode($data);
    }
}
