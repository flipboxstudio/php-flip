<?php

namespace Core\Concerns;

use DateTime;
use stdClass;
use Carbon\Carbon;

trait TypeCaster
{
    protected function toInt($input)
    {
        if (is_int($input)) {
            return $input;
        }

        return (int) $input;
    }

    protected function toFloat($input)
    {
        if (is_float($input)) {
            return $input;
        }

        return (float) $input;
    }

    protected function toDouble($input)
    {
        if (is_double($input)) {
            return $input;
        }

        return (float) $input;
    }

    protected function toString($input)
    {
        if (is_string($input)) {
            return $input;
        }

        return (string) $input;
    }

    protected function toDatetime($input)
    {
        if ($input instanceof DateTime) {
            return $input;
        }

        if (is_string($input) || is_numeric($input)) {
            return Carbon::parse($input);
        }

        return new stdClass();
    }
}
