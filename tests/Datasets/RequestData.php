<?php

namespace Tests\Datasets;

dataset(
    'all http methods',
    [
        'GET' => ['GET'],
        'POST' => ['POST'],
        'PUT' => ['PUT'],
        'PATCH' => ['PATCH'],
        'DELETE' => ['DELETE'],
        'HEAD' => ['HEAD'],
        'OPTION' => ['OPTION'],
    ]
);

dataset(
    'possible given header',
    [
        'No headers' => [[]],
        'Single header' => [['accept' => 'text/plain']],
        'Multiple header' => [['accept' => 'text/plain', 'user-agent' => fake()->userAgent()]],
    ]
);

dataset(
    'possible given query parameter',
    [
        'No query parameter' => [[]],
        'Single query parameter' => [['qParam1' => 'Value1']],
        'Multiple query parameter' => [['qParam1' => 'Value1','qParam2' => 'Value2']],
    ]
);

dataset(
    'possible given body parameter',
    [
        'No body parameter' => [[]],
        'Single body parameter' => [['bParam1' => 'Value1']],
        'Multiple body parameter' => [['bParam1' => 'Value1','bParam2' => 'Value2']],
    ]
);

