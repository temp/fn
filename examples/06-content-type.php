<?php

include __DIR__.'/../vendor/autoload.php';

use function Fnc\always;
use function Fnc\applySpec;
use function Fnc\cond;
use function Fnc\equals;
use function Fnc\flip;
use function Fnc\includes;
use function Fnc\last;
use function Fnc\pipe;
use function Fnc\prop;
use function Fnc\split;

$data = [
    'file1' => 'test.jpeg',
    'file2' => 'test.pdf',
    'file3' => 'archive.tar.gz',
];

// Derive the MIME type from the file extension. flip(includes()) turns
// "is $needle in $list?" into "is … in this list?".
$mimeType = pipe(
    split('.'),
    last(),
    cond([
        [flip(includes())(['gif', 'png', 'jpeg']), static fn (string $extension) => 'image/' . $extension],
        [equals('jpg'), always('image/jpeg')],
        [equals('pdf'), always('application/pdf')],
        [always(true), always('application/octet-stream')],
    ]),
);

$file = static fn (string $key) => applySpec([
    'name' => prop($key),
    'mimetype' => pipe(prop($key), $mimeType),
]);

$fn = applySpec([
    'file1' => $file('file1'),
    'file2' => $file('file2'),
    'file3' => $file('file3'),
]);

echo 'Input:'.PHP_EOL;
print_r($data);

echo 'Output:'.PHP_EOL;
print_r($fn($data));
