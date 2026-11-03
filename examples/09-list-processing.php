<?php

include __DIR__.'/../vendor/autoload.php';

use function Fnc\any;
use function Fnc\applySpec;
use function Fnc\chain;
use function Fnc\filter;
use function Fnc\find;
use function Fnc\flatten;
use function Fnc\groupBy;
use function Fnc\head;
use function Fnc\map;
use function Fnc\mean;
use function Fnc\none;
use function Fnc\nth;
use function Fnc\partition;
use function Fnc\pipe;
use function Fnc\pluck;
use function Fnc\prop;
use function Fnc\propEq;
use function Fnc\reduce;
use function Fnc\reject;
use function Fnc\reverse;
use function Fnc\sortBy;
use function Fnc\sum;
use function Fnc\tail;
use function Fnc\take;
use function Fnc\uniq;

// Working with a list of records instead of a single array.
$orders = [
    ['id' => 1, 'customer' => 'Ada', 'status' => 'paid', 'total' => 120.0, 'items' => ['mug', 'plate']],
    ['id' => 2, 'customer' => 'Linus', 'status' => 'cancelled', 'total' => 80.0, 'items' => ['bowl']],
    ['id' => 3, 'customer' => 'Grace', 'status' => 'paid', 'total' => 300.0, 'items' => ['mug', 'kettle']],
    ['id' => 4, 'customer' => 'Ada', 'status' => 'open', 'total' => 45.5, 'items' => ['plate']],
    ['id' => 5, 'customer' => 'Grace', 'status' => 'paid', 'total' => 99.9, 'items' => ['mug']],
];

$isCancelled = propEq('cancelled', 'status');

$report = applySpec([
    // reject() is the complement of filter(); array_values() would reindex the result
    'valid_order_ids' => pipe(reject($isCancelled), pluck('id'), array_values(...)),
    'customers' => pipe(pluck('customer'), uniq()),
    'revenue' => pipe(filter(propEq('paid', 'status')), pluck('total'), sum()),
    'average_order' => pipe(reject($isCancelled), pluck('total'), mean()),
    'top_2_orders' => pipe(
        sortBy(static fn (array $order) => -$order['total']),
        take(2),
        map(prop('id')),
    ),
    'orders_by_status' => pipe(groupBy(prop('status')), map(pluck('id'))),
    // partition() splits into [matching, others] in one pass, keys are preserved
    'paid_and_unpaid_ids' => pipe(partition(propEq('paid', 'status')), map(pluck('id'))),
    'first_open_order' => pipe(find(propEq('open', 'status')), prop('id')),
    'third_order' => pipe(nth(2), prop('id')),
    'has_open_orders' => any(propEq('open', 'status')),
    'nothing_refunded' => none(propEq('refunded', 'status')),
    // chain() maps to lists and concatenates them, flatten() does the same for already nested lists
    'all_items' => pipe(chain(prop('items')), uniq()),
    'item_count' => pipe(pluck('items'), flatten(), 'count'),
    'first_id' => pipe(pluck('id'), head()),
    'other_ids' => pipe(pluck('id'), tail()),
    'ids_newest_first' => pipe(pluck('id'), reverse()),
    // reduce() for anything the other functions don't cover
    'total_per_customer' => reduce(
        static function (array $totals, array $order) {
            $totals[$order['customer']] = ($totals[$order['customer']] ?? 0) + $order['total'];

            return $totals;
        },
        [],
    ),
]);

echo 'Input:'.PHP_EOL;
print_r($orders);

echo 'Output:'.PHP_EOL;
print_r($report($orders));
