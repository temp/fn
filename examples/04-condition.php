<?php

include __DIR__.'/../vendor/autoload.php';

use function Fnc\always;
use function Fnc\applySpec;
use function Fnc\compose;
use function Fnc\ifElse;
use function Fnc\isEmpty;
use function Fnc\prop;

$data = [
    'invalid_id' => '',
    'invalid_value' => 'hello',
    'valid_id' => 'foo',
    'valid_value' => 'bar',
];

$fn = applySpec([
    'invalid' => ifElse(
        compose(isEmpty(), prop('invalid_id')),
        always(null),
        applySpec([
            'id' => prop('invalid_id'),
            'value' => prop('invalid_value'),
        ]),
    ),
    'valid' => ifElse(
        compose(isEmpty(), prop('valid_id')),
        always(null),
        applySpec([
            'id' => prop('valid_id'),
            'value' => prop('valid_value'),
        ]),
    ),
]);

echo 'Input:'.PHP_EOL;
print_r($data);

echo 'Output:'.PHP_EOL;
print_r($fn($data));
