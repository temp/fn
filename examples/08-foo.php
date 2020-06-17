<?php

include __DIR__ . '/../vendor/autoload.php';

use function Fnc\always;
use function Fnc\applySpec;
use function Fnc\complement;
use function Fnc\filter;
use function Fnc\ifElse;
use function Fnc\isEmpty;
use function Fnc\pipe;
use function Fnc\prop;

$data = [
    'a' => 1,
    'b' => 2,
    'c' => 3,
    'd' => 1,
    'e' => null,
    'f' => 3,
    'g' => null,
    'h' => null,
    'i' => null,
];

$fn = applySpec([
    'filter1' => pipe(applySpec([prop('a'),prop('b'),prop('c')]), filter(complement(isEmpty()))),
    'value1' => ifElse(
        pipe(applySpec([prop('a'),prop('b'),prop('c')]), filter(complement(isEmpty()))),
        applySpec([
            'v1' => pipe(prop('a')),
            'v2' => pipe(prop('b')),
            'v3' => pipe(prop('c')),
        ]),
        always(null),
    ),
    'filter2' => pipe(applySpec([prop('d'),prop('e'),prop('f')]), filter(complement(isEmpty()))),
    'value2' => ifElse(
        pipe(applySpec([prop('d'),prop('e'),prop('f')]), filter(complement(isEmpty()))),
        applySpec([
            'v1' => pipe(prop('d')),
            'v2' => pipe(prop('e')),
            'v3' => pipe(prop('f')),
        ]),
        always(null),
    ),
    'filter3' => pipe(applySpec([prop('g'),prop('h'),prop('i')]), filter(complement(isEmpty()))),
    'value3' => ifElse(
        pipe(applySpec([prop('g'),prop('h'),prop('i')]), filter(complement(isEmpty()))),
        applySpec([
            'v1' => pipe(prop('g')),
            'v2' => pipe(prop('h')),
            'v3' => pipe(prop('i')),
        ]),
        always(null),
    ),
]);

echo 'Input:' . PHP_EOL;
dump($data);

echo 'Output:' . PHP_EOL;
dump($fn($data));
