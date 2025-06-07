<?php

namespace Tests\Fake;

use Nivseb\StrictRequest\Http\StrictFormRequest;

class FakeValidationRequest extends StrictFormRequest
{
    public function bodyRules(): array
    {
        return [
            'username' => ['required', 'string']
        ];
    }

    public function headerRules(): array
    {
        return [
            'x-my-header' => ['required']
        ];
    }

    public function queryRules(): array
    {
        return [
            'sorting' => ['required']
        ];
    }

}
