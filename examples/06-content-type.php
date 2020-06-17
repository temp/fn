<?php

include __DIR__.'/../vendor/autoload.php';

use function Fnc\always;
use function Fnc\flip;
use function Fnc\ifElse;
use function Fnc\pipe;
use function Fnc\prop;

$data = [
    'file_name' => 'test.jpeg',
];

$fn = pipe(
    prop('file_name'),
    static function ($value) {
        return !$value ? '' : end(explode('.', $value));
    },
    ifElse(
        flip('Fnc\contains')(['gif', 'png', 'jpg', 'jpeg']),
        static function ($value) {
            return sprintf('image/%s', $value);
        },
        always('image/png'),
    )
);

echo 'Input:'.PHP_EOL;
print_r($data);

echo 'Output:'.PHP_EOL;
print_r($fn($data));
