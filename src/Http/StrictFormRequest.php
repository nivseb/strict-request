<?php

namespace Nivseb\StrictRequest\Http;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Precognition;
use Illuminate\Validation\ValidationException;
use Nivseb\StrictRequest\Traits\HasBodyValidation;
use Nivseb\StrictRequest\Traits\HasHeaderValidation;
use Nivseb\StrictRequest\Traits\HasQueryValidation;

class StrictFormRequest extends FormRequest
{
    use HasHeaderValidation;
    use HasQueryValidation;
    use HasBodyValidation;

    /**
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws BindingResolutionException
     */
    public function validateResolved(): void
    {
        $this->prepareForValidation();

        if (!$this->passesAuthorization()) {
            $this->failedAuthorization();
        }

        $instance = $this->getValidatorInstance();

        if ($this->isPrecognitive()) {
            $instance->after(Precognition::afterValidationHook($this));
        }

        if (method_exists($this, 'headerRules')) {
            $this->validateHeader();
        }
        if (method_exists($this, 'queryRules')) {
            $this->validateQuery();
        }
        if (method_exists($this, 'bodyRules')) {
            $this->validateBody();
        }

        if ($instance->fails()) {
            $this->failedValidation($instance);
        }

        $this->passedValidation();
    }
}
