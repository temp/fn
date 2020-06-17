<?php

include __DIR__.'/../vendor/autoload.php';

use function Fnc\always;
use function Fnc\applySpec;
use function Fnc\flip;
use function Fnc\ifElse;
use function Fnc\pipe;
use function Fnc\prop;

$data = [
    'file1' => 'test.jpeg',
    'file2' => 'test.pdf',
];

$fn = applySpec([
    'file1' => [
        'name' => prop('file1'),
        'mimetype' => pipe(
            prop('file1'),
            static function ($value) {
                return !$value ? '' : end(explode('.', $value));
            },
            ifElse(
                flip('\Fnc\contains', 2)(['gif', 'png', 'jpg', 'jpeg']),
                //contains(['foo']),
                static function ($value) {
                    return sprintf('image/%s', $value);
                },
                always('application/octet-stream'),
            )
        )
    ],
    'file2' => [
        'name' => prop('file2'),
        'mimetype' => pipe(
            prop('file2'),
            static function ($value) {
                return !$value ? '' : end(explode('.', $value));
            },
            ifElse(
                flip('\Fnc\contains', 2)(['gif', 'png', 'jpg', 'jpeg']),
                //contains(['foo']),
                static function ($value) {
                    return sprintf('image/%s', $value);
                },
                always('application/octet-stream'),
            )
        )
    ],
]);

echo 'Input:'.PHP_EOL;
print_r($data);

echo 'Output:'.PHP_EOL;
print_r($fn($data));
