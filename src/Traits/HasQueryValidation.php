<?php

namespace Nivseb\StrictRequest\Traits;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Nivseb\StrictRequest\Http\StrictFormRequest;

/**
 * @mixin StrictFormRequest
 */
trait HasQueryValidation
{
    protected ?Validator $queryValidator = null;

    /**
     * @throws ValidationException
     * @throws BindingResolutionException
     */
    protected function validateQuery(): void
    {
        $instance = $this->getQueryValidatorInstance();
        if ($instance->fails()) {
            $this->failedValidation($instance);
        }
    }

    /**
     * @throws BindingResolutionException
     */
    protected function getQueryValidatorInstance(): Validator
    {
        if ($this->queryValidator) {
            return $this->queryValidator;
        }

        $factory = $this->container->make(ValidationFactory::class);

        $validator = $this->createQueryValidator($factory);

        if (method_exists($this, 'withQueryValidator')) {
            $this->withQueryValidator($validator);
        }

        if (method_exists($this, 'after')) {
            $validator->after($this->container->call(
                $this->after(...),
                ['validator' => $validator]
            ));
        }

        $this->setQueryValidator($validator);

        return $this->queryValidator;
    }

    /**
     * Set the Validator instance.
     *
     * @param Validator $validator
     * @return $this
     */
    public function setQueryValidator(Validator $validator): static
    {
        $this->queryValidator = $validator;

        return $this;
    }


    /**
     * Create the query validator instance.
     *
     * @param ValidationFactory $factory
     * @return Validator
     */
    protected function createQueryValidator(ValidationFactory $factory): Validator
    {
        $rules = $this->validationQueryRules();

        return $factory
            ->make(
                $this->query(),
                $rules,
                $this->messages(),
                $this->attributes(),
            )
            ->stopOnFirstFailure($this->stopOnFirstFailure);
    }

    protected function validationQueryRules()
    {
        return method_exists($this, 'queryRules') ? $this->container->call([$this, 'queryRules']) : [];
    }
}
