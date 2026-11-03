<?php

include __DIR__.'/../vendor/autoload.php';

use function Fnc\assoc;
use function Fnc\assocPath;
use function Fnc\concat;
use function Fnc\evolve;
use function Fnc\inc;
use function Fnc\join;
use function Fnc\keys;
use function Fnc\omit;
use function Fnc\path;
use function Fnc\paths;
use function Fnc\pick;
use function Fnc\pipe;
use function Fnc\tap;
use function Fnc\values;

$product = [
    'sku' => 'abc-123',
    'name' => '  coffee mug ',
    'price' => 12.5,
    'stock' => 3,
    'internal_note' => 'reorder soon',
    'meta' => ['views' => 41, 'tags' => ['kitchen']],
];

// All functions return modified copies, the input stays untouched.
$update = pipe(
    evolve([
        'name' => pipe('trim', 'ucwords'),
        'stock' => inc(),
        'meta' => ['views' => inc()],
    ]),
    assoc('currency', 'EUR'),
    assocPath(['meta', 'seo', 'slug'], 'coffee-mug'),
    // tap() lets you look into a pipeline without changing the value
    tap(static function (array $intermediate): void {
        echo 'After update: ' . json_encode($intermediate) . PHP_EOL;
    }),
    omit(['internal_note']),
);

$publicView = pick(['sku', 'name', 'price', 'currency']);

echo 'Input:'.PHP_EOL;
print_r($product);

$updated = $update($product);

echo 'Output:'.PHP_EOL;
print_r($updated);

echo 'Public view:'.PHP_EOL;
print_r($publicView($updated));

// Reading records
echo 'Keys: ' . join(', ', keys($updated)) . PHP_EOL;
echo 'Public values: ' . join(' | ', values($publicView($updated))) . PHP_EOL;
echo 'Views: ' . path(['meta', 'views'], $updated) . PHP_EOL;
print_r(paths([['meta', 'views'], ['meta', 'seo', 'slug'], ['meta', 'missing']], $updated));

// concat() joins lists (keys are dropped) or strings
print_r(concat(path(['meta', 'tags'], $updated), ['gift', 'ceramic']));
echo concat('SKU-', $updated['sku']) . PHP_EOL;
