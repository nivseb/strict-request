Strict Form Request
===================

[![Tests](https://img.shields.io/github/actions/workflow/status/nivseb/strict-request/test.yml?branch=main&label=Tests)](https://github.com/nivseb/strict-request/actions/workflows/tests.yml)
[![Supported PHP Version](https://badgen.net/packagist/php/nivseb/strict-request?color=8892bf)](https://www.php.net/supported-versions)
[![Latest Stable Version](https://poser.pugx.org/nivseb/strict-request/v/stable.svg)](https://packagist.org/packages/nivseb/strict-request)
[![Total Downloads](https://poser.pugx.org/nivseb/strict-request/downloads.svg)](https://packagist.org/packages/nivseb/strict-request)

This package provide a extended request class, based on the laravel `FormRequest`. This extended request class allow you
to write more restricted validation rules. By default, the original form request combine all input sources to validate
all
data based one rule set.
If you want to define on wich way the client should send the data to your application, this package is the solution.
The `StrictFormRequest` define rules for the different input sources and validate only the data from that input source
with that
validation rule set.

Example
-------

In some cases it's important for your application that some data are only include as body and not as query parameter.
In that case you can define in your request the `bodyRules` method. That will ensure, in combination with your rules,
that the data are present in the body and not in the query parameter.

Installation
------------

1. To install PHP Mock Server Connector you can easily use composer.
    ```sh
    composer require nivseb/strict-request
    ```
2. Build your request by extending from `\Nivseb\StrictRequest\Http\StrictFormRequest`
3. Write your validation by adding rules for header, body and/or query
