<?php

namespace Tests\Component\Http;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Tests\Fake\FakeValidationRequest;

test(
    'validate header and throw validation exception on fail',
    /**
     * @throws BindingResolutionException
     * @throws AuthorizationException
     */
    function (): void {
        $request = FakeValidationRequest::createFromBase(SymfonyRequest::create('/', 'POST'));
        $request->headers->add(['x-not-my-header' => 'myValue']);
        $request->query->add(['sorting' => 'Value']);
        $request->request->add(['username' => 'notMyName']);
        $app = App::getInstance();
        $request->setContainer($app);
        $request->setRedirector($app->make(Redirector::class));

        $exception = null;
        try {
            $request->validateResolved();
        } catch (ValidationException $e) {
            $exception = $e;
        }
        expect($exception->errors())
            ->toBe(
                [
                    'x-my-header' => ['The x-my-header field is required.'],
                ]
            );
    }
)
    ->with('all http methods');

test(
    'validate query and throw validation exception on fail',
    /**
     * @throws BindingResolutionException
     * @throws AuthorizationException
     */
    function (): void {
        $request = FakeValidationRequest::createFromBase(SymfonyRequest::create('/', 'POST'));
        $request->headers->add(['x-my-header' => 'myValue']);
        $request->query->add(['not-sorting' => 'Value']);
        $request->request->add(['username' => 'notMyName']);
        $app = App::getInstance();
        $request->setContainer($app);
        $request->setRedirector($app->make(Redirector::class));

        $exception = null;
        try {
            $request->validateResolved();
        } catch (ValidationException $e) {
            $exception = $e;
        }
        expect($exception->errors())
            ->toBe(
                [
                    'sorting' => ['The sorting field is required.'],
                ]
            );
    }
)
    ->with('all http methods');


test(
    'validate body and throw validation exception on fail',
    /**
     * @throws BindingResolutionException
     * @throws AuthorizationException
     */
    function (): void {
        $request = FakeValidationRequest::createFromBase(SymfonyRequest::create('/', 'POST'));
        $request->headers->add(['x-my-header' => 'myValue']);
        $request->query->add(['sorting' => 'Value']);
        $request->request->add(['not-username' => 'notMyName']);
        $app = App::getInstance();
        $request->setContainer($app);
        $request->setRedirector($app->make(Redirector::class));

        $exception = null;
        try {
            $request->validateResolved();
        } catch (ValidationException $e) {
            $exception = $e;
        }
        expect($exception->errors())
            ->toBe(
                [
                    'username' => ['The username field is required.'],
                ]
            );
    }
)
    ->with('all http methods');
