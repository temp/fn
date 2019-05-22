<?php

include __DIR__.'/../vendor/autoload.php';

use function Fn\arr;
use function Fn\compose;
use function Fn\pipe;
use function Fn\prop;
use function Fn\take;

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
