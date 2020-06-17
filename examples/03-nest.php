<?php

include __DIR__.'/../vendor/autoload.php';

use function Fnc\always;
use function Fnc\applySpec;

$data = [
    'id' => 123,
];

$fn = applySpec([
    'nested' => [
        'first' => [
            'a' => always('foo'),
            'b' => always(456),
        ],
        'second' => [
            'a' => always('bar'),
            'b' => always(567),
        ],
    ],
]);

echo 'Input:'.PHP_EOL;
print_r($data);

echo 'Output:'.PHP_EOL;
print_r($fn($data));
