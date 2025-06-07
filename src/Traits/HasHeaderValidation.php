<?php

namespace Nivseb\StrictRequest\Traits;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Nivseb\StrictRequest\Http\StrictFormRequest;

/**
 * @mixin StrictFormRequest
 */
trait HasHeaderValidation
{
    protected ?Validator $headerValidator = null;

    /**
     * @throws BindingResolutionException
     * @throws ValidationException
     */
    protected function validateHeader(): void
    {
        $instance = $this->getHeaderValidatorInstance();
        if ($instance->fails()) {
            $this->failedValidation($instance);
        }
    }

    /**
     * @throws BindingResolutionException
     */
    protected function getHeaderValidatorInstance(): Validator
    {
        if ($this->headerValidator) {
            return $this->headerValidator;
        }

        $factory = $this->container->make(ValidationFactory::class);

        $validator = $this->createHeaderValidator($factory);

        if (method_exists($this, 'withHeaderValidator')) {
            $this->withHeaderValidator($validator);
        }

        if (method_exists($this, 'after')) {
            $validator->after($this->container->call(
                $this->after(...),
                ['validator' => $validator]
            ));
        }

        $this->setHeaderValidator($validator);

        return $this->headerValidator;
    }

    /**
     * Set the Validator instance.
     *
     * @param Validator $validator
     * @return $this
     */
    public function setHeaderValidator(Validator $validator): static
    {
        $this->headerValidator = $validator;

        return $this;
    }


    /**
     * Create the header validator instance.
     *
     * @param ValidationFactory $factory
     * @return Validator
     */
    protected function createHeaderValidator(ValidationFactory $factory): Validator
    {
        $rules = $this->validationHeaderRules();

        return $factory
            ->make(
                $this->header(),
                $rules,
                $this->messages(),
                $this->attributes(),
            )
            ->stopOnFirstFailure($this->stopOnFirstFailure);
    }

    protected function validationHeaderRules()
    {
        return method_exists($this, 'headerRules') ? $this->container->call([$this, 'headerRules']) : [];
    }
}
