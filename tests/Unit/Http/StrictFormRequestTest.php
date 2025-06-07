<?php

namespace Tests\Component\Http;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Mockery;
use Mockery\Expectation;
use Nivseb\StrictRequest\Http\StrictFormRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Tests\Fake\FakeValidationRequest;

test(
    'only run default validation without strict rules',
    /**
     * @throws AuthorizationException
     * @throws BindingResolutionException
     * @throws ValidationException
     */
    function (string $httpMethod, array $headers, array $queryParameters, array $bodyParameters): void {
        $request = StrictFormRequest::createFromBase(SymfonyRequest::create('/', $httpMethod));
        $request->headers->add($headers);
        $request->query->add($queryParameters);
        $request->request->add($bodyParameters);

        $headerValidatorMock = Mockery::mock(Validator::class);
        $request->setHeaderValidator($headerValidatorMock);
        $queryValidatorMock = Mockery::mock(Validator::class);
        $request->setQueryValidator($queryValidatorMock);
        $bodyValidatorMock = Mockery::mock(Validator::class);
        $request->setBodyValidator($bodyValidatorMock);
        $validatorMock = Mockery::mock(Validator::class);
        $request->setValidator($validatorMock);

        /** @var Expectation $failsExpectation */
        $failsExpectation = $validatorMock->allows('fails');
        $failsExpectation->once()->withNoArgs()->andReturnFalse();

        $request->validateResolved();
    }
)
    ->with('all http methods')
    ->with('possible given header')
    ->with('possible given query parameter')
    ->with('possible given body parameter');

test(
    'validate successful all rule sets',
    /**
     * @throws AuthorizationException
     * @throws BindingResolutionException
     * @throws ValidationException
     */
    function (string $httpMethod, array $headers, array $queryParameters, array $bodyParameters): void {
        $request = FakeValidationRequest::createFromBase(SymfonyRequest::create('/', 'POST'));
        $request->headers->add($headers);
        $request->query->add($queryParameters);
        $request->request->add($bodyParameters);

        $headerValidatorMock = Mockery::mock(Validator::class);
        $request->setHeaderValidator($headerValidatorMock);
        $queryValidatorMock = Mockery::mock(Validator::class);
        $request->setQueryValidator($queryValidatorMock);
        $bodyValidatorMock = Mockery::mock(Validator::class);
        $request->setBodyValidator($bodyValidatorMock);
        $validatorMock = Mockery::mock(Validator::class);
        $request->setValidator($validatorMock);

        /** @var Expectation $failsHeaderExpectation */
        $failsHeaderExpectation = $headerValidatorMock->allows('fails');
        $failsHeaderExpectation->once()->withNoArgs()->andReturnFalse();

        /** @var Expectation $failsQueryExpectation */
        $failsQueryExpectation = $queryValidatorMock->allows('fails');
        $failsQueryExpectation->once()->withNoArgs()->andReturnFalse();

        /** @var Expectation $failsBodyExpectation */
        $failsBodyExpectation = $bodyValidatorMock->allows('fails');
        $failsBodyExpectation->once()->withNoArgs()->andReturnFalse();

        /** @var Expectation $failsExpectation */
        $failsExpectation = $validatorMock->allows('fails');
        $failsExpectation->once()->withNoArgs()->andReturnFalse();

        $request->validateResolved();
    }
)
    ->with('all http methods')
    ->with('possible given header')
    ->with('possible given query parameter')
    ->with('possible given body parameter');
