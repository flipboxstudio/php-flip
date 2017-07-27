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
    public const TYPE_INT = 'toInt';

    public const TYPE_FLOAT = 'toFloat';

    public const TYPE_DOUBLE = 'toDouble';

    public const TYPE_STRING = 'toString';

    public const TYPE_DATETIME = 'toDatetime';

    protected const NAMING_CAMEL = 'camel';

    protected const NAMING_STUDLY = 'studly';

    protected const NAMING_SNAKE = 'snake';

    protected const NAMING_NONE = 'none';

    protected $transformableClass;

    protected $responseClass;

    protected static $commonTransformableAttributes = [
        ['id', self::TYPE_INT],
        ['created_at', self::TYPE_DATETIME],
        ['updated_at', self::TYPE_DATETIME],
    ];

    public static function register(array $atttributes)
    {
        static::$commonTransformableAttributes = array_merge(
            static::$commonTransformableAttributes,
            $atttributes
        );
    }

    public function canTransform($model): bool
    {
        $transformableClass = $this->transformableClass();

        return $model instanceof $transformableClass;
    }

    protected function get(ModelContract $model, string $field, string $type, string $as = null): array
    {
        return [
            $as ?: $field => call_user_func_array(
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
        return (float) $input;
    }

    protected function toString($input)
    {
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
        ModelContract $model,
        string $namingStrategy = self::NAMING_NONE
    ): array {
        $attributes = [];

        foreach ($this->basicAttribute() as $attribute) {
            list($field, $type) = $attribute;

            $responseProperty = $this->resolveNaming($field, $namingStrategy);

            if ($type instanceof Closure || is_callable($type)) {
                $attributes = $attributes + [
                    $responseProperty => call_user_func_array($type, [$model]),
                ];
            } else {
                $attributes = $attributes + $this->get($model, $field, $type, $responseProperty);
            }
        }

        return $attributes;
    }

    protected function resolveNaming(string $name, string $namingStrategy): string
    {
        if ($namingStrategy === 'none') {
            return $name;
        }

        if (!method_exists(Str::class, $namingStrategy)) {
            throw new InvalidArgumentException('Invalid naming strategy.', 500);
        }

        return Str::{$namingStrategy}($name);
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

    public function transform($model): Fluent
    {
        $ref = new ReflectionClass(
            $this->responseClass()
        );

        return $ref->newInstanceArgs(
            $this->gatherResponseConstructorParameters($model)
        );
    }

    protected function gatherResponseConstructorParameters($model): array
    {
        throw new Exception('Method not implemented yet.');
    }

    protected function responseClass(): string
    {
        if (!$this->responseClass) {
            throw new Exception('responseClass property is empty.');
        }

        return $this->responseClass;
    }

    protected function transformableClass(): string
    {
        if (!$this->transformableClass) {
            throw new Exception('transformableClass property is empty.');
        }

        return $this->transformableClass;
    }
}