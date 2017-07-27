<?php

namespace Core\Util\Presenters;

use stdClass;
use DateTime;
use IteratorAggregate;
use Core\Util\Data\Fluent;
use Core\Util\Data\Collection;
use Spatie\ArrayToXml\ArrayToXml;
use Core\Contracts\Util\Presenter as PresenterContract;

class XML implements PresenterContract
{
    public function present($data)
    {
        return ArrayToXml::convert(
            $this->normalizeItem($data)
        );
    }

    protected function normalizeCollection(Collection $data): array
    {
        return $data->mapWithKeys(function ($value, $key) {
            return [
                "_{$key}" => $this->normalizeItem($value),
            ];
        })->toArray();
    }

    protected function normalizeFluent(Fluent $value): array
    {
        return $this->normalizeArray(
            $value->toArray()
        );
    }

    protected function normalizeArray($value): array
    {
        $normal = [];

        foreach ($value as $attribute => $innerValue) {
            if (is_numeric($attribute)) {
                $attribute = "_{$attribute}";
            }

            $normal[$attribute] = $this->normalizeItem($innerValue);
        }

        return $normal;
    }

    protected function normalizeItem($value)
    {
        if (is_string($value) || is_numeric($value)) {
            $value = (string) $value;
        } elseif ($value instanceof Collection) {
            $value = $this->normalizeCollection($value);
        } elseif ($value instanceof Fluent) {
            $value = $this->normalizeFluent($value);
        } elseif ($value instanceof IteratorAggregate || is_array($value)) {
            $value = $this->normalizeArray($value);
        } elseif ($value instanceof DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        } elseif ($value instanceof stdClass) {
            $value = '';
        } else {
            $value = (string) $value;
        }

        return $value;
    }
}
