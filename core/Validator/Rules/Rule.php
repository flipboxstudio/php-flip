<?php

namespace Core\Validator\Rules;

use Closure;
use Core\Contracts\Container;
use Core\Contracts\Repositories\Repository as RepositoryContract;

abstract class Rule
{
    protected $container;

    protected $attributes;

    public function __construct(Container $container, array $attributes)
    {
        $this->container = $container;
        $this->attributes = $attributes;
    }

    protected function getAttribute(string $attribute)
    {
        return (isset($this->attributes[$attribute])) ? $this->attributes[$attribute] : null;
    }

    protected function generateUniqueValidation(RepositoryContract $repository, $self = null): Closure
    {
        return function ($input, $field) use ($repository, $self) {
            $entity = $repository->findBy($field, $input);

            if (!$entity) {
                return true;
            }

            if ($self === null) {
                return false;
            }

            return $entity->get($field) === $self;
        };
    }

    protected function generateExistsValidation(RepositoryContract $repository): Closure
    {
        return function ($input, $field) use ($repository) {
            $entity = $repository->findBy($field, $input);

            return $entity !== null;
        };
    }

    protected function generateInValidation(array $validList): Closure
    {
        return function ($input) use ($validList) {
            return (!$input) || in_array($input, $validList, true);
        };
    }

    abstract public function rules(): array;
}
