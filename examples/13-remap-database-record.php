<?php

include __DIR__.'/../vendor/autoload.php';

use function Fnc\always;
use function Fnc\applySpec;
use function Fnc\ascendWith;
use function Fnc\converge;
use function Fnc\descend;
use function Fnc\descendWith;
use function Fnc\ifElse;
use function Fnc\isEmpty;
use function Fnc\join;
use function Fnc\map;
use function Fnc\omit;
use function Fnc\pipe;
use function Fnc\prop;
use function Fnc\propSatisfies;
use function Fnc\remap;
use function Fnc\slice;
use function Fnc\sortWith;
use function Fnc\unapply;

// Flat rows as they come from a SQL query with joined tables.
$rows = [
    [
        'id' => 'WS-2',
        'carhouse_id' => 'C1',
        'carhouse_name' => 'Autohaus Nord',
        'rim_brand_id' => '',
        'rim_brand_name' => '',
        'created_date' => '2026-03-04 00:00:00.000',
        'created_time' => '1753-01-01 14:30:00.000',
    ],
    [
        'id' => 'WS-1',
        'carhouse_id' => 'C1',
        'carhouse_name' => 'Autohaus Nord',
        'rim_brand_id' => 'R7',
        'rim_brand_name' => 'Rimco',
        'created_date' => '2026-03-02 00:00:00.000',
        'created_time' => '1753-01-01 08:15:00.000',
    ],
    [
        'id' => 'WS-3',
        'carhouse_id' => 'C0',
        'carhouse_name' => 'Autohaus Süd',
        'rim_brand_id' => 'R7',
        'rim_brand_name' => 'Rimco',
        'created_date' => '2026-03-01 00:00:00.000',
        'created_time' => '1753-01-01 10:00:00.000',
    ],
];

$toDto = pipe(
    remap([
        // strings move a column into the nested structure and remove it from the top level
        'carhouse' => ['id' => 'carhouse_id', 'name' => 'carhouse_name'],
        // callables get the whole row, e.g. for "null if the joined row is missing"
        'rim_brand' => ifElse(
            propSatisfies(isEmpty(), 'rim_brand_id'),
            always(null),
            applySpec(['id' => prop('rim_brand_id'), 'name' => prop('rim_brand_name')]),
        ),
        // date and time live in separate columns
        'created_at' => converge(
            unapply(join(' ')),
            [
                pipe(prop('created_date'), slice(0, 10)),
                pipe(prop('created_time'), slice(11, 19)),
            ],
        ),
    ]),
    // columns only read by callables are removed explicitly
    omit(['rim_brand_id', 'rim_brand_name', 'created_date', 'created_time']),
);

// ascendWith/descendWith take a comparison function; strcmp sorts strings byte-wise like SQL's binary collation,
// while ascend/descend use <=>, which compares numeric strings numerically
$sorted = sortWith([ascendWith(strcmp(...), prop('carhouse_id')), descend(prop('created_date'))], $rows);
$idsDescending = map(prop('id'), sortWith([descendWith(strcmp(...), prop('id'))], $rows));

echo 'Input:'.PHP_EOL;
print_r($rows);

echo 'Output:'.PHP_EOL;
print_r(map($toDto, $sorted));

echo 'IDs descending: ' . implode(', ', $idsDescending) . PHP_EOL;
