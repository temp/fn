<?php

include __DIR__.'/../vendor/autoload.php';

use function Fnc\always;
use function Fnc\applySpec;

$data = [
    'id' => 123,
];

$fn = applySpec([
    'null' => always(null),
    'string' => always('foo'),
    'int' => always(789),
    'float' => always(890.12),
]);

echo 'Input:'.PHP_EOL;
print_r($data);

echo 'Output:'.PHP_EOL;
print_r($fn($data));
