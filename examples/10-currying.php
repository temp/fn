<?php

include __DIR__.'/../vendor/autoload.php';

use function Fnc\add;
use function Fnc\apply;
use function Fnc\curry2;
use function Fnc\curryN;
use function Fnc\dec;
use function Fnc\divide;
use function Fnc\filter;
use function Fnc\flip;
use function Fnc\identity;
use function Fnc\inc;
use function Fnc\map;
use function Fnc\max;
use function Fnc\min;
use function Fnc\multiply;
use function Fnc\pipe;
use function Fnc\subtract;

// Library functions are curried: pass some arguments now, the rest later.
$addTen = add(10);
echo '$addTen(5) = ' . $addTen(5) . PHP_EOL;

// Curried functions plug directly into map(), pipe() and friends.
print_r(map(add(1), [1, 2, 3]));

// Argument order matters: subtract(10) is "10 minus …".
echo 'subtract(10)(3) = ' . subtract(10)(3) . PHP_EOL;
// flip() swaps the first two arguments, so this is "… minus 10".
$minusTen = flip(subtract())(10);
echo '$minusTen(3) = ' . $minusTen(3) . PHP_EOL;

// Curry your own functions with curryN() (or curry1() … curry5()).
$formatPrice = curryN(
    3,
    static fn (string $currency, int $decimals, float $amount) => number_format($amount, $decimals) . ' ' . $currency,
);

$euro = $formatPrice('EUR', 2);
$wholeYen = $formatPrice('JPY')(0);

echo $euro(1234.5) . PHP_EOL;
echo $wholeYen(1234.5) . PHP_EOL;

// Combine everything into reusable pipelines.
$netPriceLabels = pipe(
    map(static fn (float $gross) => $gross / 1.19),
    map($euro),
);
print_r($netPriceLabels([119.0, 59.5]));

// Curried math helpers. max(0) is "the larger of 0 and …", so it clamps negatives.
print_r(map(max(0), [-5, 3, -1]));
print_r(map(min(10), [5, 30]));
print_r(map(inc(), [1, 2]));
print_r(map(dec(), [1, 2]));
print_r(map(multiply(2), [1.5, 4]));
// divide(100) is "100 / …"; flip it for "… / 100"
print_r(map(flip(divide())(100), [50, 125]));

// identity() returns its argument, e.g. to keep only truthy values
print_r(filter(identity(), [0, 1, '', 'a', null]));

// curry1() … curry5() are shortcuts for curryN() with a fixed arity
$greet = curry2(static fn (string $greeting, string $name) => $greeting . ', ' . $name . '!');
echo $greet('Hello')('Ada') . PHP_EOL;

// apply() calls a function with an array of arguments
echo 'apply(max(), [3, 7]) = ' . apply(max(), [3, 7]) . PHP_EOL;
