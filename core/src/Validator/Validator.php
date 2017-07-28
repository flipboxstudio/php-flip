<?php

namespace Core\Validator;

use Exception;
use Core\Validator\Rules\Rule;
use Core\Exceptions\ValidationException;
use Illuminate\Validation\Validator as IlluminateValidator;
use Illuminate\Contracts\Validation\Factory as IlluminateValidatorFactory;

class Validator
{
    protected $attributes = [];

    protected $Rule;

    protected $validator;

    public function __construct(IlluminateValidatorFactory $validator)
    {
        $this->validator = $validator;
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

        if (!$rule instanceof Rule) {
            throw new Exception('Rule must be an instance of '.Rule::class.'.');
        }

        $rules = $rule->rules();

        $validator = $this->validator->make($this->attributes, $rules);

        return $validator;
    }
}
