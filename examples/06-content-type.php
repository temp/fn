<?php

include __DIR__.'/../vendor/autoload.php';

use function Fn\always;
use function Fn\contains;
use function Fn\ifElse;
use function Fn\pipe;
use function Fn\prop;

$data = [
    'file_name' => 'test.jpeg',
];

$fn = pipe(
    prop('file_name'),
    static function ($value) {
        return !$value ? '' : end(explode('.', $value));
    },
    ifElse(
        contains(['gif', 'png', 'jpg', 'jpeg']),
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
