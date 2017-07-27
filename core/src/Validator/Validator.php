<?php

namespace Core\Validator;

use Exception;
use Core\Exceptions\ValidationException;
use Core\Contracts\Container as ContainerContract;
use Illuminate\Validation\Validator as IlluminateValidator;

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

        if ($validator->passes()) {
            return true;
        }

        throw new ValidationException(
            'Some attribute(s) fail to pass validation.',
            412,
            $validator->errors()->toArray()
        );
    }

    protected function boot(): IlluminateValidator
    {
        $RuleFqn = $this->Rule;
        $rule = new $RuleFqn($this->attributes);

        if (!$rule instanceof Rules\Rule) {
            throw new Exception('Rule must be an instance of '.Rules\Rule::class.'.');
        }

        $rules = $rule->rules();

        $validator = $this->container->make('validator')->make($this->attributes, $rules);

        return $validator;
    }
}
