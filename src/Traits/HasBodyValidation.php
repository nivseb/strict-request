<?php

namespace Nivseb\StrictRequest\Traits;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Nivseb\StrictRequest\Http\StrictFormRequest;
use Symfony\Component\HttpFoundation\InputBag;

/**
 * @mixin StrictFormRequest
 */
trait HasBodyValidation
{
    protected ?Validator $bodyValidator = null;

    /**
     * @throws ValidationException
     * @throws BindingResolutionException
     */
    protected function validateBody(): void
    {
        $instance = $this->getBodyValidatorInstance();
        if ($instance->fails()) {
            $this->failedValidation($instance);
        }
    }

    /**
     * @throws BindingResolutionException
     */
    protected function getBodyValidatorInstance(): Validator
    {
        if ($this->bodyValidator) {
            return $this->bodyValidator;
        }

        $factory = $this->container->make(ValidationFactory::class);

        $validator = $this->createBodyValidator($factory);

        if (method_exists($this, 'withBodyValidator')) {
            $this->withBodyValidator($validator);
        }

        if (method_exists($this, 'after')) {
            $validator->after($this->container->call(
                $this->after(...),
                ['validator' => $validator]
            ));
        }

        $this->setBodyValidator($validator);

        return $this->bodyValidator;
    }

    /**
     * Set the Validator instance.
     *
     * @param Validator $validator
     * @return $this
     */
    public function setBodyValidator(Validator $validator): static
    {
        $this->bodyValidator = $validator;

        return $this;
    }


    /**
     * Create the body validator instance.
     *
     * @param ValidationFactory $factory
     * @return Validator
     */
    protected function createBodyValidator(ValidationFactory $factory): Validator
    {
        $rules = $this->validationBodyRules();

        $inputBag = $this->isJson() ? ($this->json() ?? []) : $this->request;

        return $factory
            ->make(
                $inputBag instanceof InputBag ? $inputBag->all() : $inputBag,
                $rules,
                $this->messages(),
                $this->attributes(),
            )
            ->stopOnFirstFailure($this->stopOnFirstFailure);
    }

    protected function validationBodyRules()
    {
        return method_exists($this, 'bodyRules') ? $this->container->call([$this, 'bodyRules']) : [];
    }
}
