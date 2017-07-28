<?php

namespace Core\Transformer\Autobots;

use Closure;
use DateTime;
use stdClass;
use Exception;
use ReflectionClass;
use Core\Util\Data\Fluent;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Support\Carbon;
use Core\Contracts\Models\Model as ModelContract;
use Core\Contracts\Transformer\Autobot as AutobotContract;

abstract class Autobot implements AutobotContract
{
    public static $namingStrategy = self::NAMING_SNAKE;

    public static $sortStrategy = self::SORT_ALPHA;

    public const TYPE_INT = 'toInt';

    public const TYPE_FLOAT = 'toFloat';

    public const TYPE_DOUBLE = 'toDouble';

    public const TYPE_STRING = 'toString';

    public const TYPE_DATETIME = 'toDatetime';

    public const NAMING_CAMEL = 'camel';

    public const NAMING_STUDLY = 'studly';

    public const NAMING_SNAKE = 'snake';

    public const NAMING_NONE = 'none';

    public const SORT_NONE = 'none';

    public const SORT_ALPHA = 'alpha';

    protected $responseClass;

    protected static $commonTransformableAttributes = [
        ['id', self::TYPE_INT],
        ['created_at', self::TYPE_DATETIME],
        ['updated_at', self::TYPE_DATETIME],
    ];

    public function transform(ModelContract $model): Fluent
    {
        $reflectionObject = new ReflectionClass(
            $this->responseClass()
        );

        return $reflectionObject->newInstanceArgs(
            $this->collectResponseInstanceArgs($model)
        );
    }

    public static function register(array $atttributes)
    {
        static::$commonTransformableAttributes = array_merge(
            static::$commonTransformableAttributes,
            $atttributes
        );
    }

    protected function sort(array $data): array
    {
        return Arr::sort($data, function ($key, $value) {
            return $key;
        });
    }

    protected function get(ModelContract $model, string $field, string $type, string $as = null): array
    {
        $as = $as ?? $this->resolveNaming($field);

        return [
            $as => call_user_func_array(
                [$this, $type],
                [$model->get($field)]
            ),
        ];
    }

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

    protected function transformBasicAttributes(
        ModelContract $model
    ): array {
        $attributes = [];

        foreach ($this->sort($this->basicAttribute()) as $attribute) {
            list($field, $type) = $attribute;
            $as = $this->resolveNaming($field);
            $merge = ($type instanceof Closure || is_callable($type))
                ? [$as => call_user_func_array($type, [$model])]
                : $this->get($model, $field, $type, $as);
            $attributes = array_merge($attributes, $merge);
        }

        return $attributes;
    }

    protected function resolveNaming(string $name): string
    {
        if (static::$namingStrategy === self::NAMING_NONE) {
            return $name;
        }

        if (!method_exists(Str::class, static::$namingStrategy)) {
            throw new InvalidArgumentException('Invalid naming strategy.', 500);
        }

        return Str::{static::$namingStrategy}($name);
    }

    protected function commonAttribute(array $pick, array $additionalAttributes = []): array
    {
        $commonTransformableAttributes = Arr::where(
            static::$commonTransformableAttributes,
            function ($commonAttribute) use ($pick) {
                list($field, $type) = $commonAttribute;

                return in_array($field, $pick);
            }
        );

        return array_merge($commonTransformableAttributes, $additionalAttributes);
    }

    protected function basicAttribute(): array
    {
        throw new Exception('Method not implemented yet.');
    }

    protected function collectResponseInstanceArgs(ModelContract $model): array
    {
        throw new Exception('Method not implemented yet.');
    }

    protected function responseClass(): string
    {
        if (!$this->responseClass) {
            throw new Exception('`responseClass` property is not set.');
        }

        return $this->responseClass;
    }
}
