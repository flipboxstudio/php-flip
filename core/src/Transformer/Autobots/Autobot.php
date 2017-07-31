<?php

namespace Core\Transformer\Autobots;

use Closure;
use Exception;
use ReflectionClass;
use Core\Util\Data\Fluent;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Core\Concerns\TypeCaster;
use Core\Contracts\Models\Model as ModelContract;
use Core\Contracts\Transformer\Autobot as AutobotContract;

abstract class Autobot implements AutobotContract
{
    use TypeCaster;

    public static $namingStrategy = self::NAMING_SNAKE;

    public const TYPE_INT = 'toInt';

    public const TYPE_FLOAT = 'toFloat';

    public const TYPE_DOUBLE = 'toDouble';

    public const TYPE_STRING = 'toString';

    public const TYPE_DATETIME = 'toDatetime';

    public const NAMING_CAMEL = 'camel';

    public const NAMING_STUDLY = 'studly';

    public const NAMING_SNAKE = 'snake';

    public const NAMING_NONE = 'none';

    protected $model;

    protected $responseClass;

    protected static $commonTransformableAttributes = [
        ['id', self::TYPE_INT],
        ['created_at', self::TYPE_DATETIME],
        ['updated_at', self::TYPE_DATETIME],
    ];

    public function bind(ModelContract $model): AutobotContract
    {
        $this->model = $model;

        return $this;
    }

    public function transform(): Fluent
    {
        $reflectionObject = new ReflectionClass(
            $this->responseClass()
        );

        return $reflectionObject->newInstanceArgs(
            $this->responseParams($this->model)
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

    protected function get(string $field, string $type, string $as = null): array
    {
        $as = $as ?? $this->resolveNaming($field);

        return [
            $as => call_user_func_array(
                [$this, $type],
                [$this->model->get($field)]
            ),
        ];
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

    protected function common(array $pick, array $additionalAttributes = []): array
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

    protected function mapping(): array
    {
        return $this->sort(
            $this->attributes()
        );
    }

    protected function attributes(): array
    {
        return [];
    }

    protected function responseParams(): array
    {
        return [];
    }

    protected function responseClass(): string
    {
        if (!$this->responseClass) {
            throw new Exception('`responseClass` property is not set.');
        }

        return $this->responseClass;
    }

    protected function transformFromMapping(): array
    {
        $attributes = [];

        foreach ($this->mapping() as $attribute) {
            list($field, $type) = $attribute;

            $as = $this->resolveNaming($field);

            $merge = ($type instanceof Closure || is_callable($type))
                ? [$as => call_user_func_array($type, [$this->model])]
                : $this->get($field, $type, $as);

            $attributes = array_merge($attributes, $merge);
        }

        return $attributes;
    }
}
