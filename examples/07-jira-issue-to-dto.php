<?php

include __DIR__.'/../vendor/autoload.php';

use function Fnc\always;
use function Fnc\applySpec;
use function Fnc\converge;
use function Fnc\divide;
use function Fnc\filter;
use function Fnc\flip;
use function Fnc\ifElse;
use function Fnc\isEmpty;
use function Fnc\isNil;
use function Fnc\map;
use function Fnc\nth;
use function Fnc\path;
use function Fnc\pathSatisfies;
use function Fnc\pick;
use function Fnc\pipe;
use function Fnc\prop;
use function Fnc\propOr;
use function Fnc\split;
use function Fnc\unless;
use function Fnc\values;

// A ticket as returned by an issue tracker's REST API: standard fields plus custom fields ("__…").
// Option fields are objects with a "value", user fields have a "displayName", amounts are in cents,
// percentages in basis points.
$issue = [
    'key' => 'DEAL-42',
    'summary' => 'Office building Hafenstraße',
    'status' => ['name' => 'In Review'],
    'created' => '2026-05-12T09:30:00.000+0200',
    'creator' => ['displayName' => 'Ada Lovelace'],
    '__Purchase price' => 1250000000,
    '__Price type' => 'L',
    '__Yield' => 475,
    '__Occupancy' => 9200,
    '__Area' => 1250000,
    '__Area unit' => ['value' => 'ft²'],
    '__Usage' => ['value' => 'Office'],
    '__Year of construction' => '1998',
    '__Year of refurbishment' => '',
    '__Market value' => null,
    '__Coordinates' => '53.5461,9.9661',
    '__Closing date' => '2026-08-15',
    '__Allocation' => [
        'funds' => [
            ['fund' => 'FUND-A', 'proposed' => true, 'vote' => 'yes'],
            ['fund' => 'FUND-B', 'proposed' => false, 'vote' => null],
            ['fund' => 'FUND-C', 'proposed' => true, 'vote' => 'no'],
        ],
    ],
    'comment' => [
        'comments' => [
            [
                'created' => '2026-05-13T10:00:00.000+0200',
                'author' => ['displayName' => 'Grace Hopper'],
                'body' => 'Rent roll checked.',
            ],
            [
                'created' => '2026-05-14T16:45:00.000+0200',
                'author' => ['displayName' => 'Ada Lovelace'],
                'body' => 'Ready for committee.',
            ],
        ],
    ],
];

const PRICE_TYPES = ['F' => 'final', 'L' => 'letter of intent', 'A' => 'initial offer'];
const SQM_PER_UNIT = ['m²' => 1.0, 'ft²' => 0.09290304, 'tsubo' => 3.30579];

// Small domain helpers that the library doesn't (and shouldn't) provide.
// flip(divide())(100) is "… / 100": cents to euro, basis points to percent.
$fromHundredths = flip(divide())(100);
$quarter = static fn (string $date) => sprintf('Q%d/%s', ceil((int) substr($date, 5, 2) / 3), substr($date, 0, 4));
$toSquareMeters = static fn (float $area, string $unit) => round($area * SQM_PER_UNIT[$unit], 2);

$toDto = applySpec([
    'key' => prop('key'),
    'name' => prop('summary'),
    'status' => path(['status', 'name']),
    'created' => prop('created'),
    'creator' => path(['creator', 'displayName']),
    'usage' => path(['__Usage', 'value']),
    'purchase_price' => [
        'euro' => pipe(prop('__Purchase price'), $fromHundredths),
        // flip(prop()) looks the code up in the map
        'type' => pipe(prop('__Price type'), flip(prop())(PRICE_TYPES)),
    ],
    'yield_percent' => pipe(prop('__Yield'), $fromHundredths),
    'occupancy_percent' => pipe(prop('__Occupancy'), $fromHundredths),
    'area_sqm' => converge($toSquareMeters, [
        pipe(prop('__Area'), $fromHundredths),
        path(['__Area unit', 'value']),
    ]),
    // internal functions like intval can be used directly
    'year_of_construction' => pipe(prop('__Year of construction'), ifElse(isEmpty(), always(null), 'intval')),
    'year_of_refurbishment' => pipe(prop('__Year of refurbishment'), ifElse(isEmpty(), always(null), 'intval')),
    // unless() keeps null as null instead of turning it into 0
    'market_value' => pipe(prop('__Market value'), unless(isNil(), $fromHundredths)),
    'location' => [
        'lat' => pipe(prop('__Coordinates'), split(','), nth(0), 'floatval'),
        'lng' => pipe(prop('__Coordinates'), split(','), nth(1), 'floatval'),
    ],
    'closing' => ifElse(
        pathSatisfies(isEmpty(), ['__Closing date']),
        always(null),
        applySpec([
            'date' => prop('__Closing date'),
            'quarter' => pipe(prop('__Closing date'), $quarter),
        ]),
    ),
    'proposed_funds' => pipe(
        path(['__Allocation', 'funds']),
        filter(prop('proposed')),
        map(pick(['fund', 'vote'])),
        values(),
    ),
    'comments' => pipe(
        path(['comment', 'comments']),
        map(applySpec([
            'created' => prop('created'),
            'author' => path(['author', 'displayName']),
            'text' => prop('body'),
        ])),
    ),
    'labels' => propOr([], 'labels'),
]);

echo 'Input:'.PHP_EOL;
print_r($issue);

echo 'Output:'.PHP_EOL;
print_r($toDto($issue));
