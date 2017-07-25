<?php

namespace Core\Validator;

use Core\Exceptions\ValidationException;
use SimpleValidator\Validator as SimpleValidator;
use Core\Contracts\Container as ContainerContract;

class Validator
{
    protected $attributes = [];

    protected $container;

    protected $Rule;

    public function __construct(ContainerContract $container)
    {
        $this->container = $container;
    }

    public function prepare(string $Rule, array $attributes): Validator
    {
        $this->Rule = $Rule;
        $this->attributes = $attributes;

        return $this;
    }

    public function validate(): bool
    {
        $validator = $this->boot();

        if ($validator->isSuccess()) {
            return true;
        }

        throw new ValidationException('Some attribute(s) fail to pass validation.', 412, $validator->getErrors());
    }

    protected function boot(): SimpleValidator
    {
        $rule = $this->container->makeWith($this->Rule, ['attributes' => $this->attributes]);
        $rules = $rule->rules();

        $validator = SimpleValidator::validate($this->attributes, $rules);

        $validator->customErrors([
            'unique' => 'The :attribute has already been taken.',
            'exists' => 'The selected :attribute is invalid.',
            'in' => 'The selected :attribute is invalid.',
        ]);

        return $validator;
    }
}
