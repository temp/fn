# fnc

[![Build](https://github.com/temp/fnc/actions/workflows/build.yml/badge.svg?branch=main)](https://github.com/temp/fnc/actions/workflows/build.yml)
[![codecov](https://codecov.io/gh/temp/fnc/branch/main/graph/badge.svg)](https://codecov.io/gh/temp/fnc)
[![Latest Stable Version](https://poser.pugx.org/temp/fnc/version)](https://packagist.org/packages/temp/fnc)
[![License](https://poser.pugx.org/temp/fnc/license)](https://packagist.org/packages/temp/fnc)
[![Total Downloads](https://img.shields.io/packagist/dt/temp/fnc)](https://packagist.org/packages/temp/fnc)

A [Ramda](https://ramdajs.com/)-inspired functional programming library for PHP.

All functions live in the `Fnc` namespace, are plain functions (no classes) and most of them are
**auto-curried**: call them with fewer arguments than they need and you get a function waiting for the rest.
That makes it easy to build data transformations out of small pieces with `pipe()`, `compose()` and
`applySpec()`.

```php
use function Fnc\filter;
use function Fnc\map;
use function Fnc\pipe;
use function Fnc\prop;

$adultNames = pipe(
    filter(fn (array $user) => $user['age'] >= 18),
    map(prop('name')),
);

$adultNames([
    ['name' => 'Ada', 'age' => 36],
    ['name' => 'Linus', 'age' => 17],
]); // [0 => 'Ada']
```

## Installation

```bash
composer require temp/fnc
```

The package was called `temp/fn` up to 1.0 and is abandoned in favour of `temp/fnc`, which also provides the older
versions. `temp/fnc` replaces `temp/fn`, Composer never installs both. The namespace `Fnc` is unchanged.

Requires PHP 8.4 or later. The functions are loaded via Composer's `files` autoloading, so importing them with
`use function Fnc\…;` is all you need.

## Currying

Curried functions accept their arguments all at once or one (or several) at a time:

```php
use function Fnc\prop;

prop('name', ['name' => 'Ada']); // 'Ada'
prop('name')(['name' => 'Ada']); // 'Ada'

$getName = prop('name');
$getName(['name' => 'Ada']);     // 'Ada'
```

Arguments are ordered "data last" like in Ramda, so the configuration comes first and the data you pipe through
comes last. Extra arguments beyond the function's arity are ignored.

You can curry your own functions with `curry1()` … `curry5()` or `curryN()`:

```php
use function Fnc\curryN;

$add3 = curryN(3, fn ($a, $b, $c) => $a + $b + $c);

$add3(1)(2)(3); // 6
$add3(1, 2)(3); // 6
```

Where the library determines a callable's arity itself (`applySpec`, `ifElse`, `flip`, `pipe`), it counts the
**required** parameters only, like `Function.length` in JavaScript: `trim` has an arity of 1 and
`fn ($a, $b = 1)` as well. Pass an explicit arity (`curryN(2, $fn)`, `flip($fn, 2)`) if optional parameters should
be curried too.

## Building transformations

`applySpec()` builds an array of the same shape as the spec, with each function applied to the input.
Nested specs work as well:

```php
use function Fnc\applySpec;
use function Fnc\path;
use function Fnc\prop;

$toDto = applySpec([
    'name' => prop('name'),
    'adult' => fn (array $user) => $user['age'] >= 18,
    'location' => [
        'city' => path(['address', 'city']),
    ],
]);

$toDto(['name' => 'Linus', 'age' => 17, 'address' => ['city' => 'Helsinki']]);
// ['name' => 'Linus', 'adult' => false, 'location' => ['city' => 'Helsinki']]
```

`ifElse()` branches on a condition. A `null` branch returns the argument unchanged:

```php
use function Fnc\ifElse;

$trimStrings = ifElse(fn ($value) => is_string($value), 'trim', null);

$trimStrings(' x '); // 'x'
$trimStrings(5);     // 5
```

`converge()` feeds the same input to several functions and combines their results:

```php
use function Fnc\converge;
use function Fnc\divide;
use function Fnc\sum;

$average = converge(divide(), [sum(), 'count']);

$average([1, 2, 3, 4]); // 2.5
```

`remap()` is not part of Ramda, it is specific to this library. It restructures flat records such as database rows. A string in the spec names a key of the record: its
value is moved there and the key disappears from the result. Arrays are nested specs, callables receive the whole
record (like in `applySpec()`), all other keys are kept:

```php
use function Fnc\always;
use function Fnc\applySpec;
use function Fnc\ifElse;
use function Fnc\isEmpty;
use function Fnc\prop;
use function Fnc\propSatisfies;
use function Fnc\remap;

$toDto = remap([
    'wheelhotel' => ['id' => 'wheelhotel_id', 'name' => 'wheelhotel_name'],
    'rim_brand' => ifElse(
        propSatisfies(isEmpty(), 'rim_brand_id'),
        always(null),
        applySpec(['id' => prop('rim_brand_id'), 'name' => prop('rim_brand_name')]),
    ),
]);

$toDto(['id' => 7, 'wheelhotel_id' => 'W1', 'wheelhotel_name' => 'North', 'rim_brand_id' => '', 'rim_brand_name' => '']);
// ['wheelhotel' => ['id' => 'W1', 'name' => 'North'], 'rim_brand' => null,
//  'id' => 7, 'rim_brand_id' => '', 'rim_brand_name' => '']
```

Keys only read by callables (`rim_brand_*` above) stay in the record; combine with `omit()` if they should go.
Note that an `omit()` after `remap()` runs on the result, so don't list keys the spec produces there.

When migrating from `converge(merge(), [applySpec([...]), omit([...])])`: there, a record key that wasn't omitted won
over a computed key of the same name. With `remap()` the computed key wins.
Because strings are always keys, pass named functions as closures (`trim(...)`). Arrays are always nested specs,
so wrap array callables like `[$object, 'method']` in a closure as well.

More complete examples are in the [`examples/`](examples) directory, run them e.g. with
`php examples/09-list-processing.php`:

| Example | Shows |
| --- | --- |
| `01-remap.php` … `05-pipe.php` | Remapping a single array with `applySpec`, nesting, conditions, `pipe`/`compose` |
| `06-content-type.php` | Deriving a MIME type with `split`, `last`, `cond` and `flip(includes())` |
| `07-jira-issue-to-dto.php` | An issue tracker ticket with custom fields mapped to a DTO: `path`, unit conversions with `flip(divide())`, `converge`, `unless`/`ifElse` for optional values, `filter`/`pick` on nested lists |
| `08-optional-groups.php` | Outputting a group of fields only if one of them is filled |
| `09-list-processing.php` | Lists of records: `filter`/`reject`/`partition`, `find`, `pluck`, `sortBy`, `groupBy`, `uniq`, `chain`/`flatten`, `nth`, `head`/`tail`, `all`/`any`/`none`, `sortWith` with `ascend`/`descend`, `mean`, `reduce` |
| `10-currying.php` | Partial application, argument order, `flip`, `curryN`/`curry2`, curried math (`min`/`max`, `inc`/`dec`, `multiply`/`divide`), `identity`, `apply` |
| `11-conditions.php` | Predicates and defaults: `propEq`/`pathEq`, `gt`/`lt`, `allPass`/`anyPass`, `both`/`either`, `has`, `not`, `propSatisfies`/`pathSatisfies`, `cond`, `when`/`unless`, `propOr`/`pathOr`/`defaultTo`, `logicAnd`/`logicOr`/`logicXor`, `emptyVal`/`isEmpty` |
| `12-updating-records.php` | Immutable updates with `evolve`, `assoc`, `assocPath`, `pick`, `omit`, `tap`; reading with `keys`/`values`, `path`/`paths`; `concat` |
| `13-remap-database-record.php` | Turning flat SQL rows into nested DTOs with `remap`, `propSatisfies`, `slice`, `sortWith` with `ascendWith`/`descendWith` |

## Functions

C = curried.

### Function

| Function | C | Description |
| --- | --- | --- |
| `always($value)` | | Returns a function that always returns `$value`. |
| `apply($fn, $args)` | ✓ | Calls `$fn` with the elements of `$args` as arguments. |
| `applySpec($spec)` | ✓ | Returns a function that builds an array shaped like `$spec`, applying each function in it to the arguments. |
| `ascend($fn, $a, $b)` | ✓ | Comparator for `sortWith()`: `$fn($a) <=> $fn($b)` (numeric strings compare numerically). |
| `ascendWith($compare, $fn, $a, $b)` | ✓ | Like `ascend()`, but compares with `$compare`, e.g. `strcmp(...)` for byte-wise string order. |
| `complement($fn)` | | Returns a function returning the negated result of `$fn`. |
| `compose(...$fns)` | | Right-to-left composition. The rightmost function may have any arity, the others must be unary. |
| `converge($after, $fns)` | ✓ | Applies all `$fns` to the arguments and passes their results to `$after`. |
| `curry1($fn)` … `curry5($fn)` | | Curries a function of arity 1 to 5. |
| `curryN($arity, $fn)` | | Curries `$fn` with the given arity (0 to 5). |
| `descend($fn, $a, $b)` | ✓ | Comparator for `sortWith()`: `$fn($b) <=> $fn($a)`. |
| `descendWith($compare, $fn, $a, $b)` | ✓ | Like `descend()`, but compares with `$compare`. |
| `flip($fn, $arity = null)` | | Returns `$fn` with the first two arguments swapped, curried. |
| `identity($value)` | ✓ | Returns `$value`. |
| `pipe(...$fns)` | | Left-to-right composition. The first function may have any arity, the others must be unary. |
| `tap($fn, $value)` | ✓ | Calls `$fn($value)` and returns `$value`, e.g. for debugging inside `pipe()`. |
| `unapply($fn)` | | Returns a variadic function passing its arguments to `$fn` as one array. |

### List

| Function | C | Description |
| --- | --- | --- |
| `all($predicate, $list)` | ✓ | `true` if all elements match. Stops at the first mismatch. |
| `any($predicate, $list)` | ✓ | `true` if at least one element matches. Stops at the first match. |
| `chain($fn, $list)` | ✓ | Maps each element to a list and concatenates the results (flatMap). |
| `concat($a, $b)` | ✓ | Concatenates two lists (keys are dropped) or two strings. |
| `contains($needle, $list)` | ✓ | **Deprecated**, use `includes()`. `true` if `$list` contains `$needle` (strict comparison, objects by identity). |
| `drop($n, $listOrString)` | ✓ | Removes the first `$n` elements or characters. |
| `filter($predicate, $list)` | ✓ | Keeps the elements matching `$predicate`. Keys are preserved. The key is passed as second argument if the predicate accepts it. |
| `find($predicate, $list)` | ✓ | First matching element, `null` if none. |
| `flatten($list)` | ✓ | Flattens nested arrays into one list. |
| `groupBy($fn, $list)` | ✓ | Groups the elements by the key `$fn` returns, each group is a list. |
| `head($listOrString)` | ✓ | First element (`null` for an empty list) or first character. |
| `includes($needle, $listOrString)` | ✓ | `true` if the list contains an element equal to `$needle` (in `equals()` terms) or the string contains it. |
| `join($separator, $list)` | ✓ | `implode()`. |
| `last($listOrString)` | ✓ | Last element (`null` for an empty list) or last character. |
| `map($fn, $list)` | ✓ | Applies `$fn` to each value. Keys are preserved, `null` returns `[]`. |
| `merge($a, $b)` | ✓ | `array_merge()`: for string keys the right value wins, numeric keys are appended. |
| `none($predicate, $list)` | ✓ | `true` if no element matches. |
| `nth($offset, $listOrString)` | ✓ | Element or character at the position, negative counts from the end. |
| `partition($predicate, $list)` | ✓ | `[$matching, $others]`, keys preserved. |
| `pluck($key, $list)` | ✓ | `prop($key)` of each element. |
| `reduce($fn, $acc, $list)` | ✓ | Folds the list with `$fn($acc, $value)`. The key is passed as third argument if `$fn` accepts it. |
| `reject($predicate, $list)` | ✓ | The complement of `filter()`. |
| `reverse($list)` | ✓ | Reverses the list. |
| `slice($from, $to, $listOrString)` | ✓ | Elements or characters from `$from` to `$to` (exclusive), negative counts from the end, `$to = null` until the end. |
| `sortBy($fn, $list)` | ✓ | Sorts ascending by the values `$fn` returns (stable). Returns a list. |
| `sortWith($comparators, $list)` | ✓ | Sorts by several comparators (`ascend()`/`descend()`), the next one breaks ties. Returns a list. |
| `split($separator, $string)` | ✓ | `explode()`. |
| `sum($list)` | ✓ | Sum of all elements, `0` for an empty list. |
| `tail($listOrString)` | ✓ | All but the first element or character. |
| `take($n, $listOrString)` | ✓ | The first `$n` elements or characters. |
| `uniq($list)` | ✓ | Removes duplicates (in `equals()` terms), keeps the first occurrence. Returns a list. |

### Object / array access

| Function | C | Description |
| --- | --- | --- |
| `assoc($key, $value, $array)` | ✓ | Copy with `$key` set to `$value`. |
| `assocPath($path, $value, $array)` | ✓ | Copy with the nested key set, missing levels are created. |
| `evolve($transformations, $array)` | ✓ | Copy with the transformations applied to the matching (nested) keys. |
| `has($key, $arrayOrObject)` | ✓ | `true` if the key or public property exists, even with a `null` value. |
| `keys($arrayOrObject)` | ✓ | Keys or public property names. |
| `omit($keys, $array)` | ✓ | Removes the given keys. |
| `path($path, $array)` | ✓ | Nested value, e.g. `path(['address', 'city'])`. `null` if any key is missing. |
| `pathOr($default, $path, $array)` | ✓ | `path()` with a default for missing or `null` values. |
| `paths($paths, $array)` | ✓ | Several `path()` lookups at once. |
| `pick($keys, $array)` | ✓ | Only the given keys, in their order. |
| `prop($key, $arrayOrObject)` | ✓ | Array key or public object property, `null` if missing. |
| `propOr($default, $key, $arrayOrObject)` | ✓ | `prop()` with a default for missing or `null` values. |
| `remap($spec, $record)` | ✓ | *Not in Ramda.* Restructures a flat record: strings in the spec move that key there (and remove it), see below. |
| `values($arrayOrObject)` | ✓ | Values as list, or public property values. |

### Logic

| Function | C | Description |
| --- | --- | --- |
| `allPass($predicates)` | ✓ | Predicate satisfied if all `$predicates` are. |
| `anyPass($predicates)` | ✓ | Predicate satisfied if at least one of `$predicates` is. |
| `both($f, $g)` | ✓ | Predicate satisfied if `$f` and `$g` are. |
| `cond($pairs)` | ✓ | Calls the transformer of the first `[$predicate, $transformer]` pair that matches, `null` if none. |
| `defaultTo($default, $value)` | ✓ | `$value`, or `$default` if it is `null` or `NAN`. |
| `either($f, $g)` | ✓ | Predicate satisfied if `$f` or `$g` is. |
| `emptyVal($value)` | ✓ | The empty value of `$value`'s type: `''`, `0`, `0.0`, `false`, `[]`, `new stdClass()`, an empty closure or `null`. |
| `ifElse($condition, $onTrue, $onFalse)` | ✓ | Returns a function calling `$onTrue` or `$onFalse` depending on `$condition`. `null` branches return the first argument. |
| `isEmpty($value)` | ✓ | `true` if `$value` equals the empty value of its type, e.g. `''`, `[]`, `0`, `null`. |
| `isNil($value)` | ✓ | `true` if `$value` is `null`. |
| `pathSatisfies($predicate, $path, $array)` | ✓ | `true` if the nested value satisfies the predicate. |
| `propSatisfies($predicate, $key, $arrayOrObject)` | ✓ | `true` if the property satisfies the predicate. |
| `logicAnd($a, $b)` | ✓ | `$a && $b`. |
| `logicOr($a, $b)` | ✓ | `$a \|\| $b`. |
| `logicXor($a, $b)` | ✓ | `$a xor $b`. |
| `not($value)` | ✓ | `!$value`. |
| `unless($predicate, $fn, $value)` | ✓ | `$value` if the predicate matches, otherwise `$fn($value)`. |
| `when($predicate, $fn, $value)` | ✓ | `$fn($value)` if the predicate matches, otherwise `$value`. |

### Relation

The first argument is the left operand: `gt(5)` means "5 is greater than …". Use `lt(5)` for "… is greater than 5".

| Function | C | Description |
| --- | --- | --- |
| `equals($a, $b)` | ✓ | Deep equality, see below. |
| `gt($a, $b)`, `gte($a, $b)` | ✓ | `$a > $b`, `$a >= $b`. |
| `lt($a, $b)`, `lte($a, $b)` | ✓ | `$a < $b`, `$a <= $b`. |
| `max($a, $b)`, `min($a, $b)` | ✓ | The larger / smaller of two values. |
| `pathEq($value, $path, $array)` | ✓ | `true` if the nested value equals `$value`. Value first, like Ramda ≥ 0.29. |
| `propEq($value, $key, $arrayOrObject)` | ✓ | `true` if the property equals `$value`. Value first, like Ramda ≥ 0.29. |

### Math

| Function | C | Description |
| --- | --- | --- |
| `add($a, $b)` | ✓ | `$a + $b`. `null` counts as `0`. |
| `dec($n)`, `inc($n)` | ✓ | `$n - 1`, `$n + 1`. |
| `divide($a, $b)` | ✓ | `$a / $b`. `null` counts as `0`, division by zero returns `INF`, `-INF` or `NAN` like in Ramda. |
| `mean($list)` | ✓ | Arithmetic mean, `NAN` for an empty list. |
| `multiply($a, $b)` | ✓ | `$a * $b`. `null`, `false` and `''` count as `0`. |
| `subtract($a, $b)` | ✓ | `$a - $b`. `null` counts as `0`. |

Functions prefixed with an underscore (`_getArity()`, `_arity()`, …) are internal and may change at any time.

## Semantics worth knowing

- **`equals()`** compares scalars strictly (`1` ≠ `1.0` ≠ `'1'`; unlike Ramda, as PHP has separate int and float
  types), treats `NAN` as equal to `NAN` like Ramda, compares lists by position (`[1, 2]` ≠ `[2, 1]`), associative
  arrays independent of key order, objects only if they are of the same class with equal properties (cyclic
  references are fine), and closures by identity. `includes()` and `uniq()` use it as well.
- **`uniq()`** compares each element with all kept ones, which is quadratic. For large lists of scalars,
  `array_unique()` is faster.
- **`isEmpty()`** differs from Ramda for `0` and `null`, which are considered empty here.
- **Strings** in `head()`, `tail()`, `last()`, `nth()`, `take()`, `drop()` and `slice()` are handled byte-wise like
  `substr()`, so multibyte characters such as `Ä` can be cut in half.
- **`filter()`** keeps the keys of the input. Use `array_values()` if you need a list again.
- **Callbacks** for `filter()` and `reduce()` only receive the key when they declare a parameter for it, so internal
  functions such as `is_numeric` or `trim` can be passed directly.

## Development

```bash
composer install
vendor/bin/phpunit           # tests
vendor/bin/phpstan analyze   # static analysis
vendor/bin/phpcs             # coding standard (vendor/bin/phpcbf fixes most issues)
```

Commit messages follow [Conventional Commits](https://www.conventionalcommits.org/). Releases are created
automatically by [semantic-release](https://semantic-release.gitbook.io/) when changes land on `main`:
`fix:` makes a patch release, `feat:` a minor release.

## License

MIT
