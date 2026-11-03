<?php

include __DIR__.'/../vendor/autoload.php';

use function Fnc\allPass;
use function Fnc\always;
use function Fnc\anyPass;
use function Fnc\applySpec;
use function Fnc\both;
use function Fnc\cond;
use function Fnc\defaultTo;
use function Fnc\either;
use function Fnc\emptyVal;
use function Fnc\gt;
use function Fnc\gte;
use function Fnc\has;
use function Fnc\isEmpty;
use function Fnc\isNil;
use function Fnc\logicAnd;
use function Fnc\logicOr;
use function Fnc\logicXor;
use function Fnc\lt;
use function Fnc\lte;
use function Fnc\map;
use function Fnc\not;
use function Fnc\pathEq;
use function Fnc\pathOr;
use function Fnc\pathSatisfies;
use function Fnc\pipe;
use function Fnc\prop;
use function Fnc\propEq;
use function Fnc\propOr;
use function Fnc\propSatisfies;
use function Fnc\unless;
use function Fnc\when;

$users = [
    ['name' => ' Ada ', 'age' => 36, 'role' => 'admin', 'nickname' => 'Countess', 'address' => ['city' => 'London']],
    ['name' => 'Linus', 'age' => 17, 'role' => 'user'],
    ['name' => null, 'age' => 70, 'role' => 'user', 'address' => ['city' => '']],
];

// Note the operand order of curried comparisons: lt(17) reads "17 < …", lte(65) reads "65 <= …".
$isAdult = pipe(prop('age'), lt(17));
$isSenior = pipe(prop('age'), lte(65));
$isMinor = pipe(prop('age'), gte(17));

$toCard = applySpec([
    // unless() keeps null as it is, when() only transforms matching values
    'name' => pipe(prop('name'), unless(isNil(), 'trim'), when(isNil(), always('(unknown)'))),
    'city' => pathOr('–', ['address', 'city']),
    'role' => propOr('guest', 'role'),
    'can_vote' => $isAdult,
    'discount' => either($isMinor, $isSenior),
    'admin_access' => allPass([$isAdult, propEq('admin', 'role')]),
    'nickname' => pipe(prop('nickname'), defaultTo('n/a')),
    // gt(50) reads "50 > …"
    'under_50' => pipe(prop('age'), gt(50)),
    'london_admin' => both(pathEq('London', ['address', 'city']), propEq('admin', 'role')),
    'has_address' => has('address'),
    'no_address' => pipe(has('address'), not()),
    // a missing or empty city both count as incomplete
    'incomplete' => anyPass([propSatisfies(isNil(), 'name'), pathSatisfies(isEmpty(), ['address', 'city'])]),
    'group' => cond([
        [$isMinor, always('junior')],
        [$isSenior, always('senior')],
        [always(true), always('regular')],
    ]),
]);

echo 'Input:'.PHP_EOL;
print_r($users);

echo 'Output:'.PHP_EOL;
print_r(map($toCard, $users));

// Boolean operators as curried functions
var_export([logicAnd(true, false), logicOr(true)(false), logicXor(true, true)]);
echo PHP_EOL;

// emptyVal() is the empty value of a type; isEmpty() compares a value against it
var_export([emptyVal('abc'), emptyVal([1, 2]), emptyVal(42), isEmpty(0), isEmpty('0')]);
echo PHP_EOL;
