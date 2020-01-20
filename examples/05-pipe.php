<?php

include __DIR__.'/../vendor/autoload.php';

use function Fnc\arr;
use function Fnc\compose;
use function Fnc\pipe;
use function Fnc\prop;
use function Fnc\take;

$data = [
    'abc' => 'def',
    'foo' => 'bar',
];

$fn = arr([
    'abc' => pipe(prop('abc'), take(1)),
    'foo' => compose(take(1), prop('foo')),
]);

echo 'Input:'.PHP_EOL;
print_r($data);

echo 'Output:'.PHP_EOL;
print_r($fn($data));
