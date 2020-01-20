<?php

include __DIR__.'/../vendor/autoload.php';

use function Fnc\arr;
use function Fnc\compose;
use function Fnc\converge;
use function Fnc\drop;
use function Fnc\ifElse;
use function Fnc\isEmpty;
use function Fnc\join;
use function Fnc\merge;
use function Fnc\omit;
use function Fnc\pipe;
use function Fnc\prop;
use function Fnc\take;

$data = [
    'id' => 123,
    'contact_id' => 234,
    'contact_name' => 'abc',
    'unloading_point_code' => 345,
    'unloading_point_description' => 'def',
    'creation_date_date' => '2019-01-01 00:00:00.000',
    'creation_date_time' => '1700-01-01 10:11:12.000',
];

$fn = converge(
    merge(),
    [
        arr([
            'contact' => arr([
                'id' => prop('contact_id'),
                'name' => prop('contact_name'),
            ]),
            'unloadingPoint' => ifElse(
                compose(isEmpty(), prop('unloading_point_code')),
                null,
                arr([
                    'id' => prop('unloading_point_code'),
                    'value' => prop('unloading_point_description'),
                ]),
            ),
            'creationDate' => converge(
                join(' '),
                [
                    pipe(prop('creation_date_date'), take(10)),
                    pipe(prop('creation_date_time'), drop(11), take(8)),
                ]
            ),
        ]),
        omit([
            'contact_id', 'contact_name',
            'unloading_point_code', 'unloading_point_description',
            'creation_date_date', 'creation_date_time',
        ]),
    ]
);

echo 'Input:'.PHP_EOL;
print_r($data);

echo 'Output:'.PHP_EOL;
print_r($fn($data));
