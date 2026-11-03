<?php

declare(strict_types=1);

namespace Fnc;

use Closure;
use InvalidArgumentException;
use ReflectionFunction;
use RuntimeException;
use stdClass;
use WeakMap;

use function array_column;
use function array_diff_key;
use function array_filter;
use function array_key_exists;
use function array_key_first;
use function array_key_last;
use function array_keys;
use function array_map;
use function array_merge;
use function array_reduce;
use function array_reverse;
use function array_shift;
use function array_slice;
use function array_sum;
use function array_values;
use function call_user_func_array;
use function count;
use function explode;
use function fdiv;
use function func_get_args;
use function func_num_args;
use function get_object_vars;
use function implode;
use function in_array;
use function is_array;
use function is_bool;
use function is_callable;
use function is_float;
use function is_int;
use function is_nan;
use function is_numeric;
use function is_object;
use function is_string;
use function max as phpMax;
use function min as phpMin;
use function spl_object_id;
use function str_contains;
use function strlen;
use function substr;
use function usort;

use const ARRAY_FILTER_USE_BOTH;

// phpcs:disable BrainbitsCodingStandard.Exceptions.GlobalException.GlobalException
// phpcs:disable SlevomatCodingStandard.Commenting.ForbiddenAnnotations.AnnotationForbidden
// phpcs:disable Squiz.Functions.GlobalFunction.Found

/**
 * Adds two numbers. `null` counts as 0.
 *
 * Signature: `add($a, $b)`, curried.
 *
 * @category Math
 * @example  add(2, 3); // 5
 */
function add(mixed ...$args): mixed
{
    $_add = static function ($a, $b) {
        return ($a ?? 0) + ($b ?? 0);
    };

    return curry2($_add)(...$args);
}

/**
 * Returns `true` if all elements of the list match the predicate, `false` if there are any
 * that don't.
 * Function "breaks early"
 *
 * Signature: `all(callable $fn, array $list)`, curried.
 *
 * @category List
 */
function all(mixed ...$args): mixed
{
    $_all = static function (callable $fn, array $list) {
        foreach ($list as $item) {
            if (!apply($fn, [$item])) {
                return false;
            }
        }

        return true;
    };

    return curry2($_all)(...$args);
}

/**
 * Takes a list of predicates and returns a predicate that is satisfied if all of them are satisfied by the
 * given arguments. Stops at the first predicate that isn't satisfied.
 *
 * Signature: `allPass(array $predicates)`, curried.
 *
 * @category Logic
 */
function allPass(mixed ...$args): mixed
{
    $_allPass = static function (array $predicates) {
        return static function (...$args) use ($predicates): bool {
            foreach ($predicates as $predicate) {
                if (!$predicate(...$args)) {
                    return false;
                }
            }

            return true;
        };
    };

    return curry1($_allPass)(...$args);
}

/**
 * Returns a function that always returns the given value, whatever arguments it gets. Objects are returned as the
 * same instance.
 *
 * @category Function
 */
function always(mixed $arg): callable
{
    return static function () use ($arg) {
        return $arg;
    };
}

/**
 * Returns `true` if at least one of the elements of the list match the predicate, `false` otherwise.
 * Function "breaks early"
 *
 * Signature: `any(callable $fn, array $list)`, curried.
 *
 * @category List
 */
function any(mixed ...$args): mixed
{
    $_any = static function (callable $fn, array $list) {
        foreach ($list as $item) {
            if (apply($fn, [$item])) {
                return true;
            }
        }

        return false;
    };

    return curry2($_any)(...$args);
}

/**
 * Takes a list of predicates and returns a predicate that is satisfied if at least one of them is satisfied by
 * the given arguments. Stops at the first predicate that is satisfied.
 *
 * Signature: `anyPass(array $predicates)`, curried.
 *
 * @category Logic
 */
function anyPass(mixed ...$args): mixed
{
    $_anyPass = static function (array $predicates) {
        return static function (...$args) use ($predicates): bool {
            foreach ($predicates as $predicate) {
                if ($predicate(...$args)) {
                    return true;
                }
            }

            return false;
        };
    };

    return curry1($_anyPass)(...$args);
}

/**
 * Applies function fn to the argument list args. This is useful for creating a fixed-arity function from a
 * variadic function.
 *
 * Signature: `apply(callable $callable, array $list)`, curried.
 *
 * @category Function
 */
function apply(mixed ...$args): mixed
{
    $_apply = static function (callable $callable, array $list) {
        return $callable(...$list);
    };

    return curry2($_apply)(...$args);
}

/**
 * Returns a function that builds an array shaped like the spec: each callable in the spec is called with the
 * arguments, nested arrays are nested specs. The result is curried with the highest arity of the callables.
 *
 * @category Function
 * @example  applySpec(['name' => prop('name'), 'city' => path(['address', 'city'])]);
 */
function applySpec(mixed $spec): mixed
{
    $_applySpec = static function (array $spec) {
        $mapValues = static function (callable $fn, $obj) {
            return array_reduce(
                array_keys($obj),
                static function ($acc, $key) use ($fn, $obj) {
                    $acc[$key] = $fn($obj[$key]);

                    return $acc;
                },
                [],
            );
        };

        $spec = $mapValues(
            static function ($v) {
                return is_array($v) ? applySpec($v) : $v;
            },
            $spec,
        );

        $arity = 0;
        foreach ($spec as $f) {
            $arity = phpMax($arity, _getArity($f));
        }

        return curryN(
            $arity,
            static function (...$args) use ($spec, $mapValues) {
                return $mapValues(
                    static function ($f) use ($args) {
                        return $f(...$args);
                    },
                    $spec,
                );
            },
        );
    };

    return curry1($_applySpec)($spec);
}

/**
 * Comparator for `sortWith()`: compares two values by the result of the function, ascending.
 *
 * Signature: `ascend(callable $fn, $a, $b): int`, curried.
 *
 * @category Function
 * @example  sortWith([ascend(prop('carhouse_id')), descend(prop('date'))], $records);
 */
function ascend(mixed ...$args): mixed
{
    $_ascend = static function (callable $fn, $a, $b): int {
        return $fn($a) <=> $fn($b);
    };

    return curry3($_ascend)(...$args);
}

/**
 * Returns a copy of the array with the key set to the value.
 *
 * Signature: `assoc(string|int $key, $value, array $array)`, curried.
 *
 * @category Object
 * @example  assoc('b', 2, ['a' => 1]); // ['a' => 1, 'b' => 2]
 */
function assoc(mixed ...$args): mixed
{
    $_assoc = static function (string|int $key, $value, array $array) {
        $array[$key] = $value;

        return $array;
    };

    return curry3($_assoc)(...$args);
}

/**
 * Returns a copy of the array with the nested key set to the value. Missing levels are created as arrays, non
 * array values on the way are replaced.
 *
 * Signature: `assocPath(array $path, $value, $array) use (&$_assocPath)`, curried.
 *
 * @category Object
 * @example  assocPath(['a', 'b'], 1, []); // ['a' => ['b' => 1]]
 */
function assocPath(mixed ...$args): mixed
{
    $_assocPath = static function (array $path, $value, $array) use (&$_assocPath) {
        if ($path === []) {
            return $value;
        }

        $key = array_shift($path);
        $array = is_array($array) ? $array : [];
        $array[$key] = $_assocPath($path, $value, $array[$key] ?? null);

        return $array;
    };

    return curry3($_assocPath)(...$args);
}

/**
 * Returns a predicate that is satisfied if both predicates are satisfied. The second predicate is only called if
 * the first one is satisfied.
 *
 * Signature: `both(callable $f, callable $g)`, curried.
 *
 * @category Logic
 */
function both(mixed ...$args): mixed
{
    $_both = static function (callable $f, callable $g) {
        return static fn (...$args): bool => $f(...$args) && $g(...$args);
    };

    return curry2($_both)(...$args);
}

/**
 * Maps each element to a list and concatenates the results (flatMap). Returns a list.
 *
 * Signature: `chain(callable $fn, array $list)`, curried.
 *
 * @category List
 * @example  chain(fn ($n) => [$n, $n], [1, 2]); // [1, 1, 2, 2]
 */
function chain(mixed ...$args): mixed
{
    $_chain = static function (callable $fn, array $list) {
        $result = [];
        foreach ($list as $value) {
            foreach ($fn($value) as $item) {
                $result[] = $item;
            }
        }

        return $result;
    };

    return curry2($_chain)(...$args);
}

/**
 * Returns a function that returns the negated result of the given function.
 *
 * @category Function
 */
function complement(callable $fn): callable
{
    return static function (...$args) use ($fn): bool {
        return !$fn(...$args);
    };
}

/**
 * Performs right-to-left function composition. The rightmost function may have any arity; the remaining functions
 * must be unary.
 *
 * @category Function
 */
function compose(callable ...$args): callable
{
    if ($args === []) {
        throw new InvalidArgumentException('compose requires at least one argument');
    }

    return pipe(...reverse($args));
}

/**
 * Concatenates two lists or two strings. Lists are treated as lists: the result contains the values of `$a`
 * followed by the values of `$b`, keys are not preserved. Use `merge()` for arrays with string keys.
 *
 * Signature: `concat(array|string $a, array|string $b)`, curried.
 *
 * @category List
 * @example  concat([1, 2], [3]); // [1, 2, 3]
 * @example  concat('ab', 'cd'); // 'abcd'
 */
function concat(mixed ...$args): mixed
{
    $_concat = static function (array|string $a, array|string $b) {
        if (is_string($a) && is_string($b)) {
            return $a . $b;
        }

        if (is_array($a) && is_array($b)) {
            return [...array_values($a), ...array_values($b)];
        }

        throw new InvalidArgumentException('concat expects two arrays or two strings');
    };

    return curry2($_concat)(...$args);
}

/**
 * Takes a list of [predicate, transformer] pairs and returns a function that calls the transformer of the first
 * pair whose predicate is satisfied. Returns null if no predicate matches; use `always(true)` as the last
 * predicate for a default.
 *
 * Signature: `cond(array $pairs)`, curried.
 *
 * @category Logic
 */
function cond(mixed ...$args): mixed
{
    $_cond = static function (array $pairs) {
        return static function (...$args) use ($pairs) {
            foreach ($pairs as [$predicate, $transformer]) {
                if ($predicate(...$args)) {
                    return $transformer(...$args);
                }
            }

            return null;
        };
    };

    return curry1($_cond)(...$args);
}

/**
 * Returns true if the list contains the value, compared strictly (`in_array(…, true)`): objects by identity,
 * arrays only if identical including key order.
 *
 * Signature: `contains($needle, array $list)`, curried.
 *
 * @deprecated Use includes(), which compares with equals() and also works on strings.
 *
 * @category List
 */
function contains(mixed ...$args): mixed
{
    $_contains = static function ($needle, array $list) {
        return in_array($needle, $list, true);
    };

    return curry2($_contains)(...$args);
}

/**
 * Returns a function that calls each of the branch functions with its arguments and passes their results to the
 * converging function.
 *
 * Signature: `converge(callable $after, array $branches)`, curried.
 *
 * @category Function
 * @example  converge(divide(), [sum(), 'count']); // average
 */
function converge(mixed ...$args): callable
{
    $_converge = static function (callable $fn, array $funcs) {
        return static function (...$args) use ($fn, $funcs) {
            $flippedApply = flip(apply(), 2);
            $applyItemsOverFunctions = map($flippedApply($args));

            return apply($fn, $applyItemsOverFunctions($funcs));
        };
    };

    return curry2($_converge)(...$args);
}

/**
 * Curries a function of one argument: called without arguments it returns itself.
 *
 * @category Function
 */
function curry1(callable $fn): callable
{
    $_curry1 = static function () use ($fn, &$_curry1) {
        $args = func_get_args();

        switch (count($args)) {
            case 0:
                return $_curry1;

            case 1:
                return call_user_func_array($fn, $args);

            default:
                return call_user_func_array($fn, [$args[0]]);
        }
    };

    _curriedArity($_curry1, 1);

    return $_curry1;
}

/**
 * Curries a function of two arguments: arguments can be passed all at once or one at a time.
 *
 * @category Function
 */
function curry2(callable $fn): callable
{
    $_curry2 = static function () use ($fn, &$_curry2) {
        $args = func_get_args();

        switch (func_num_args()) {
            case 0:
                return $_curry2;

            case 1:
                return static function ($b) use ($args, $fn) {
                    return call_user_func_array($fn, [$args[0], $b]);
                };

            case 2:
                return call_user_func_array($fn, $args);

            default:
                return call_user_func_array($fn, [$args[0], $args[1]]);
        }
    };

    _curriedArity($_curry2, 2);

    return $_curry2;
}

/**
 * Curries a function of three arguments: arguments can be passed all at once or in several calls.
 *
 * @category Function
 */
function curry3(callable $fn): callable
{
    $_curry3 = static function () use ($fn, &$_curry3) {
        $args = func_get_args();

        switch (func_num_args()) {
            case 0:
                return $_curry3;

            case 1:
                return curry2(static function ($b, $c) use ($args, $fn) {
                    return call_user_func_array($fn, [$args[0], $b, $c]);
                });

            case 2:
                return static function ($c) use ($args, $fn) {
                    return call_user_func_array($fn, [$args[0], $args[1], $c]);
                };

            case 3:
                return call_user_func_array($fn, $args);

            default:
                return call_user_func_array($fn, [$args[0], $args[1], $args[2]]);
        }
    };

    _curriedArity($_curry3, 3);

    return $_curry3;
}

/**
 * Curries a function of four arguments: arguments can be passed all at once or in several calls.
 *
 * @category Function
 */
function curry4(callable $fn): callable
{
    $_curry4 = static function () use ($fn, &$_curry4) {
        $args = func_get_args();

        switch (func_num_args()) {
            case 0:
                return $_curry4;

            case 1:
                return curry3(static function ($b, $c, $d) use ($args, $fn) {
                    return call_user_func_array($fn, [$args[0], $b, $c, $d]);
                });

            case 2:
                return curry2(static function ($c, $d) use ($args, $fn) {
                    return call_user_func_array($fn, [$args[0], $args[1], $c, $d]);
                });

            case 3:
                return static function ($d) use ($args, $fn) {
                    return call_user_func_array($fn, [$args[0], $args[1], $args[2], $d]);
                };

            case 4:
                return call_user_func_array($fn, $args);

            default:
                return call_user_func_array($fn, [$args[0], $args[1], $args[2], $args[3]]);
        }
    };

    _curriedArity($_curry4, 4);

    return $_curry4;
}

/**
 * Curries a function of five arguments: arguments can be passed all at once or in several calls.
 *
 * @category Function
 */
function curry5(callable $fn): callable
{
    $_curry5 = static function () use ($fn, &$_curry5) {
        $args = func_get_args();

        switch (func_num_args()) {
            case 0:
                return $_curry5;

            case 1:
                return curry4(static function ($b, $c, $d, $e) use ($args, $fn) {
                    return call_user_func_array($fn, [$args[0], $b, $c, $d, $e]);
                });

            case 2:
                return curry3(static function ($c, $d, $e) use ($args, $fn) {
                    return call_user_func_array($fn, [$args[0], $args[1], $c, $d, $e]);
                });

            case 3:
                return curry2(static function ($d, $e) use ($args, $fn) {
                    return call_user_func_array($fn, [$args[0], $args[1], $args[2], $d, $e]);
                });

            case 4:
                return static function ($e) use ($args, $fn) {
                    return call_user_func_array($fn, [$args[0], $args[1], $args[2], $args[3], $e]);
                };

            case 5:
                return call_user_func_array($fn, $args);

            default:
                return call_user_func_array($fn, [$args[0], $args[1], $args[2], $args[3], $args[4]]);
        }
    };

    _curriedArity($_curry5, 5);

    return $_curry5;
}

/**
 * Wrapper for curry* functions.
 *
 * @category Function
 */
function curryN(int $arity, callable $callable): callable
{
    switch ($arity) {
        case 0:
            return $callable;

        case 1:
            return curry1($callable);

        case 2:
            return curry2($callable);

        case 3:
            return curry3($callable);

        case 4:
            return curry4($callable);

        case 5:
            return curry5($callable);

        default:
            throw new RuntimeException('unsupported arity ' . $arity);
    }
}

/**
 * Decrements a number by 1.
 *
 * Signature: `dec($value)`, curried.
 *
 * @category Math
 */
function dec(mixed ...$args): mixed
{
    $_dec = static function ($value) {
        return $value - 1;
    };

    return curry1($_dec)(...$args);
}

/**
 * Returns the value, or the default if the value is `null` or `NAN`.
 *
 * Signature: `defaultTo($default, $value)`, curried.
 *
 * @category Logic
 * @example  defaultTo(42, null); // 42
 */
function defaultTo(mixed ...$args): mixed
{
    $_defaultTo = static function ($default, $value) {
        if ($value === null || (is_float($value) && is_nan($value))) {
            return $default;
        }

        return $value;
    };

    return curry2($_defaultTo)(...$args);
}

/**
 * Comparator for `sortWith()`: compares two values by the result of the function, descending.
 *
 * Signature: `descend(callable $fn, $a, $b): int`, curried.
 *
 * @category Function
 */
function descend(mixed ...$args): mixed
{
    $_descend = static function (callable $fn, $a, $b): int {
        return $fn($b) <=> $fn($a);
    };

    return curry3($_descend)(...$args);
}

/**
 * Divides two numbers. Equivalent to `$a / $b`, like Ramda: `null`, `false` and `''` count as 0, and dividing
 * by zero (also `'0'`) returns INF, -INF or NAN instead of throwing.
 *
 * Signature: `divide($a, $b)`, curried.
 */
function divide(mixed ...$args): mixed
{
    $_divide = static function ($a, $b) {
        $orZero = static fn ($value) => $value === null || $value === false || $value === '' ? 0 : $value;
        $a = $orZero($a);
        $b = $orZero($b);

        if (is_numeric($a) && is_numeric($b) && (float) $b === 0.0) {
            return fdiv((float) $a, (float) $b);
        }

        return $a / $b;
    };

    return curry2($_divide)(...$args);
}

/**
 * Removes the first `$count` elements of a list (like `array_slice()`: string keys are kept, numeric keys
 * renumbered) or characters of a string.
 *
 * Signature: `drop($count, $list)`, curried.
 *
 * @category List
 */
function drop(mixed ...$args): mixed
{
    $_drop = static function ($count, $list) {
        if (is_array($list)) {
            return array_slice($list, $count);
        }

        return substr($list, $count);
    };

    return curry2($_drop)(...$args);
}

/**
 * Returns a predicate that is satisfied if at least one of the predicates is satisfied. The second predicate is
 * only called if the first one isn't satisfied.
 *
 * Signature: `either(callable $f, callable $g)`, curried.
 *
 * @category Logic
 */
function either(mixed ...$args): mixed
{
    $_either = static function (callable $f, callable $g) {
        return static fn (...$args): bool => $f(...$args) || $g(...$args);
    };

    return curry2($_either)(...$args);
}

/**
 * Returns the empty value of its argument's type.
 *
 * @category Function
 */
function emptyVal(mixed ...$args): mixed
{
    $_emptyVal = static function ($value) {
        if (is_string($value)) {
            return '';
        }

        if (is_array($value)) {
            return [];
        }

        if (is_object($value) && is_callable($value)) {
            return static function (): void {
            };
        }

        if (is_object($value)) {
            return new stdClass();
        }

        if (is_int($value)) {
            return 0;
        }

        if (is_float($value)) {
            return 0.0;
        }

        if (is_bool($value)) {
            return false;
        }

        return null;
    };

    return curry1($_emptyVal)(...$args);
}

/**
 * Deep equality: scalars are compared strictly (unlike in Ramda, `1` and `1.0` differ, as PHP has separate int and
 * float types), `NAN` equals `NAN` like in Ramda, arrays by key (list order matters, order of string keys
 * doesn't), objects only if they are of the same class and all their properties are equal. Closures are
 * compared by identity. Cyclic object graphs are supported.
 */
function equals(mixed ...$args): mixed
{
    $compare = static function ($actual, $expected, array &$seen) use (&$compare): bool {
        if ($actual === $expected) {
            return true;
        }

        if (is_float($actual) && is_float($expected) && is_nan($actual) && is_nan($expected)) {
            return true;
        }

        if (is_object($actual) || is_object($expected)) {
            if (!is_object($actual) || !is_object($expected)) {
                return false;
            }

            if ($actual::class !== $expected::class || $actual instanceof Closure) {
                return false;
            }

            // a pair that is already being compared further up is assumed equal, which ends cycles
            $pair = spl_object_id($actual) . ':' . spl_object_id($expected);
            if (isset($seen[$pair])) {
                return true;
            }

            $seen[$pair] = true;

            $actual = (array) $actual;
            $expected = (array) $expected;
        }

        if (!is_array($actual) || !is_array($expected)) {
            return false;
        }

        if (count($actual) !== count($expected)) {
            return false;
        }

        foreach ($actual as $key => $value) {
            if (!array_key_exists($key, $expected) || !$compare($value, $expected[$key], $seen)) {
                return false;
            }
        }

        return true;
    };

    $_equals = static function ($a, $b) use ($compare) {
        $seen = [];

        return $compare($a, $b, $seen);
    };

    return curry2($_equals)(...$args);
}

/**
 * Returns a copy of the array with the transformations applied to the matching keys. A nested array of
 * transformations is applied to the nested array. Keys missing in the input are not added.
 * Callables in arrays (`[$object, 'method']`) would be taken for nested transformations, wrap them in a closure.
 *
 * Signature: `evolve(array $transformations, array $array) use (&$_evolve)`, curried.
 *
 * @category Object
 * @example  evolve(['count' => inc()], ['count' => 1, 'name' => 'a']); // ['count' => 2, 'name' => 'a']
 */
function evolve(mixed ...$args): mixed
{
    $_evolve = static function (array $transformations, array $array) use (&$_evolve) {
        foreach ($transformations as $key => $transformation) {
            if (!array_key_exists($key, $array)) {
                continue;
            }

            if (is_array($transformation)) {
                if (is_array($array[$key])) {
                    $array[$key] = $_evolve($transformation, $array[$key]);
                }
            } elseif (is_callable($transformation)) {
                $array[$key] = $transformation($array[$key]);
            }
        }

        return $array;
    };

    return curry2($_evolve)(...$args);
}

/**
 * Keeps the values matching the predicate. Keys are preserved; the key is passed as second argument if the predicate
 * accepts it.
 *
 * Signature: `filter(callable $callable, array $list)`, curried.
 *
 * @category List
 */
function filter(mixed ...$args): mixed
{
    $_filter = static function (callable $callable, array $list) {
        if (_acceptsArgs($callable, 2)) {
            return array_filter($list, $callable, ARRAY_FILTER_USE_BOTH);
        }

        return array_filter($list, $callable);
    };

    return curry2($_filter)(...$args);
}

/**
 * Returns the first element matching the predicate, `null` if there is none.
 *
 * Signature: `find(callable $predicate, array $list)`, curried.
 *
 * @category List
 */
function find(mixed ...$args): mixed
{
    $_find = static function (callable $predicate, array $list) {
        foreach ($list as $value) {
            if ($predicate($value)) {
                return $value;
            }
        }

        return null;
    };

    return curry2($_find)(...$args);
}

/**
 * Flattens nested arrays into one list, recursively.
 *
 * Signature: `flatten(array $list) use (&$_flatten)`, curried.
 *
 * @category List
 * @example  flatten([1, [2, [3, 4]]]); // [1, 2, 3, 4]
 */
function flatten(mixed ...$args): mixed
{
    $_flatten = static function (array $list) use (&$_flatten) {
        $result = [];
        foreach ($list as $value) {
            if (is_array($value)) {
                foreach ($_flatten($value) as $item) {
                    $result[] = $item;
                }
            } else {
                $result[] = $value;
            }
        }

        return $result;
    };

    return curry1($_flatten)(...$args);
}

/**
 * Returns a new function much like the supplied one, except that the first two arguments' order is reversed.
 *
 * @return callable
 *
 * @category Function
 */
function flip(callable $callable, ?int $arity = null) // phpcs:ignore
{
    if ($arity === null) {
        // a variadic or unary callable still gets its first two arguments flipped
        $arity = phpMax(2, _getArity($callable));
    }

    return curryN($arity, static function () use ($callable) {
        $args = func_get_args();

        return apply($callable, concat([$args[1], $args[0]], tail(tail($args))));
    });
}

/**
 * Groups the elements by the key the function returns for them. Each group is a list.
 *
 * Signature: `groupBy(callable $fn, array $list)`, curried.
 *
 * @category List
 * @example  groupBy(fn ($n) => $n % 2 ? 'odd' : 'even', [1, 2, 3]); // ['odd' => [1, 3], 'even' => [2]]
 */
function groupBy(mixed ...$args): mixed
{
    $_groupBy = static function (callable $fn, array $list) {
        $groups = [];
        foreach ($list as $value) {
            $groups[$fn($value)][] = $value;
        }

        return $groups;
    };

    return curry2($_groupBy)(...$args);
}

/**
 * `$a > $b`. Note the argument order when curried: `gt(5)` is "5 is greater than …", use `lt(5)` for
 * "… is greater than 5".
 *
 * Signature: `gt($a, $b): bool`, curried.
 *
 * @category Relation
 */
function gt(mixed ...$args): mixed
{
    $_gt = static function ($a, $b): bool {
        return $a > $b;
    };

    return curry2($_gt)(...$args);
}

/**
 * `$a >= $b`.
 *
 * Signature: `gte($a, $b): bool`, curried.
 *
 * @category Relation
 */
function gte(mixed ...$args): mixed
{
    $_gte = static function ($a, $b): bool {
        return $a >= $b;
    };

    return curry2($_gte)(...$args);
}

/**
 * Whether the array has the key or the object has the public property, even if its value is `null`.
 *
 * Signature: `has(string|int $key, $item): bool`, curried.
 *
 * @category Object
 */
function has(mixed ...$args): mixed
{
    $_has = static function (string|int $key, $item): bool {
        if (is_array($item)) {
            return array_key_exists($key, $item);
        }

        if (is_object($item)) {
            return array_key_exists($key, get_object_vars($item));
        }

        return false;
    };

    return curry2($_has)(...$args);
}

/**
 * The first element of a list (`null` if empty, keys are ignored) or the first character of a string.
 *
 * Signature: `head(array|string $list)`, curried.
 *
 * @category List
 */
function head(mixed ...$args): mixed
{
    $_head = static function (array|string $list) {
        if (is_string($list)) {
            return substr($list, 0, 1);
        }

        if ($list === []) {
            return null;
        }

        return $list[array_key_first($list)];
    };

    return curry1($_head)(...$args);
}

/**
 * Returns its argument unchanged.
 *
 * Signature: `identity($value)`, curried.
 *
 * @category Function
 */
function identity(mixed ...$args): mixed
{
    $_identity = static function ($identity) {
        return $identity;
    };

    return curry1($_identity)(...$args);
}

/**
 * Creates a function that calls $onTrue if $condition is satisfied, $onFalse otherwise.
 * A null branch returns the first argument unchanged.
 *
 * Signature: `ifElse(callable $condition, callable|null $onTrue, callable|null $onFalse)`, curried.
 */
function ifElse(mixed ...$args): callable
{
    $_ifElse = static function (callable $condition, callable|null $onTrue, callable|null $onFalse) {
        $arity = phpMax(
            _getArity($condition),
            $onTrue !== null ? _getArity($onTrue) : 0,
            $onFalse !== null ? _getArity($onFalse) : 0,
        );

        $identity = static fn (...$args) => $args[0] ?? null;
        $onTrue ??= $identity;
        $onFalse ??= $identity;

        $__ifElse = static function (...$args) use ($condition, $onTrue, $onFalse) {
            return $condition(...$args) ? $onTrue(...$args) : $onFalse(...$args);
        };

        return curryN($arity, $__ifElse);
    };

    return curry3($_ifElse)(...$args);
}

/**
 * Increments a number by 1.
 *
 * Signature: `inc($value)`, curried.
 *
 * @category Math
 */
function inc(mixed ...$args): mixed
{
    $_inc = static function ($value) {
        return $value + 1;
    };

    return curry1($_inc)(...$args);
}

/**
 * Whether the list contains an element equal to the needle (in `equals()` terms), or the string contains the
 * needle as substring.
 *
 * Signature: `includes($needle, array|string $haystack): bool`, curried.
 *
 * @category List
 * @example  includes(['a' => 1], [['a' => 1], ['b' => 2]]); // true
 * @example  includes('ell', 'hello'); // true
 */
function includes(mixed ...$args): mixed
{
    $_includes = static function ($needle, array|string $haystack): bool {
        if (is_string($haystack)) {
            return is_string($needle) && str_contains($haystack, $needle);
        }

        foreach ($haystack as $value) {
            if (equals($needle, $value)) {
                return true;
            }
        }

        return false;
    };

    return curry2($_includes)(...$args);
}

/**
 * Whether the value equals the empty value of its type (see `emptyVal()`), e.g. `''`, `[]`, `0`, `null` or an object
 * without properties. Unlike Ramda, `0` and `null` count as empty.
 *
 * Signature: `isEmpty($value)`, curried.
 *
 * @category Logic
 */
function isEmpty(mixed ...$args): mixed
{
    $_isEmpty = static function ($value): bool {
        return equals($value, emptyVal($value)) === true;
    };

    return curry1($_isEmpty)(...$args);
}

/**
 * Whether the value is `null`.
 *
 * Signature: `isNil($value): bool`, curried.
 *
 * @category Type
 */
function isNil(mixed ...$args): mixed
{
    $_isNil = static function ($value): bool {
        return $value === null;
    };

    return curry1($_isNil)(...$args);
}

/**
 * Returns a string made by inserting the separator between each element and concatenating all the elements into a
 * single string.
 *
 * Signature: `join(string $separator, array $list)`, curried.
 *
 * @category List
 */
function join(mixed ...$args): mixed
{
    $_join = static function (string $separator, array $list) {
        return implode($separator, $list);
    };

    return curry2($_join)(...$args);
}

/**
 * The keys of an array, or the names of an object's public properties.
 *
 * Signature: `keys(array|object $item)`, curried.
 *
 * @category Object
 */
function keys(mixed ...$args): mixed
{
    $_keys = static function (array|object $item) {
        return array_keys(is_object($item) ? get_object_vars($item) : $item);
    };

    return curry1($_keys)(...$args);
}

/**
 * The last element of a list (`null` if empty) or the last character of a string.
 *
 * Signature: `last(array|string $list)`, curried.
 *
 * @category List
 */
function last(mixed ...$args): mixed
{
    $_last = static function (array|string $list) {
        if (is_string($list)) {
            return substr($list, -1);
        }

        if ($list === []) {
            return null;
        }

        return $list[array_key_last($list)];
    };

    return curry1($_last)(...$args);
}

/**
 * `$a && $b`.
 *
 * Signature: `logicAnd($a, $b)`, curried.
 *
 * @category Logic
 */
function logicAnd(mixed ...$args): mixed
{
    $_and = static function ($a, $b) {
        return $a && $b;
    };

    return curry2($_and)(...$args);
}

/**
 * `$a || $b`.
 *
 * Signature: `logicOr($a, $b)`, curried.
 *
 * @category Logic
 */
function logicOr(mixed ...$args): mixed
{
    $_or = static function ($a, $b) {
        return $a || $b;
    };

    return curry2($_or)(...$args);
}

/**
 * `$a xor $b`.
 *
 * Signature: `logicXor($a, $b)`, curried.
 *
 * @category Logic
 */
function logicXor(mixed ...$args): mixed
{
    $_xor = static function ($a, $b) {
        return $a xor $b;
    };

    return curry2($_xor)(...$args);
}

/**
 * `$a < $b`. When curried, `lt(5)` reads "5 is less than …", i.e. "… is greater than 5".
 *
 * Signature: `lt($a, $b): bool`, curried.
 *
 * @category Relation
 */
function lt(mixed ...$args): mixed
{
    $_lt = static function ($a, $b): bool {
        return $a < $b;
    };

    return curry2($_lt)(...$args);
}

/**
 * `$a <= $b`.
 *
 * Signature: `lte($a, $b): bool`, curried.
 *
 * @category Relation
 */
function lte(mixed ...$args): mixed
{
    $_lte = static function ($a, $b): bool {
        return $a <= $b;
    };

    return curry2($_lte)(...$args);
}

/**
 * Applies the function to every value of the list. Keys are preserved, `null` returns `[]`. The function only
 * receives the value.
 *
 * Signature: `map(callable $fn, array|null $list)`, curried.
 *
 * @category List
 */
function map(mixed ...$args): mixed
{
    $_map = static function (callable $fn, array|null $list) {
        if (!is_array($list)) {
            return [];
        }

        return array_map($fn, $list);
    };

    return curry2($_map)(...$args);
}

/**
 * The larger of two values.
 *
 * Signature: `max($a, $b)`, curried.
 *
 * @category Relation
 */
function max(mixed ...$args): mixed
{
    $_max = static function ($a, $b) {
        return $b > $a ? $b : $a;
    };

    return curry2($_max)(...$args);
}

/**
 * The arithmetic mean of the list, `NAN` for an empty list.
 *
 * Signature: `mean(array $list)`, curried.
 *
 * @category Math
 */
function mean(mixed ...$args): mixed
{
    $_mean = static function (array $list) {
        return fdiv(array_sum($list), count($list));
    };

    return curry1($_mean)(...$args);
}

/**
 * Curried array_merge() of two arrays: for string keys the value of $b wins, numeric keys are appended.
 * Use concat() to join lists.
 *
 * Signature: `merge(array $a, array $b)`, curried.
 *
 * @category List
 */
function merge(mixed ...$args): mixed
{
    $_merge = static function (array $a, array $b) {
        return array_merge($a, $b);
    };

    return curry2($_merge)(...$args);
}

/**
 * The smaller of two values.
 *
 * Signature: `min($a, $b)`, curried.
 *
 * @category Relation
 */
function min(mixed ...$args): mixed
{
    $_min = static function ($a, $b) {
        return $b < $a ? $b : $a;
    };

    return curry2($_min)(...$args);
}

/**
 * Multiplies two numbers. Equivalent to `a * b` but curried.
 *
 * Signature: `multiply($a, $b)`, curried.
 */
function multiply(mixed ...$args): mixed
{
    $_multiply = static function ($a, $b) {
        // null, false and '' count as 0; numbers keep their type, so multiply(0.0, 1.5) is 0.0
        $orZero = static fn ($value) => $value === null || $value === false || $value === '' ? 0 : $value;

        return $orZero($a) * $orZero($b);
    };

    return curry2($_multiply)(...$args);
}

/**
 * Returns `true` if no elements of the list match the predicate, `false` otherwise.
 * Function "breaks early"
 *
 * Signature: `none(callable $fn, array $list)`, curried.
 *
 * @category List
 */
function none(mixed ...$args): mixed
{
    $_none = static function (callable $fn, array $list) {
        return all(complement($fn), $list);
    };

    return curry2($_none)(...$args);
}

/**
 * A function that returns the `!` of its argument. It will return `true` when
 * passed false-y value, and `false` when passed a truth-y one.
 *
 * Signature: `not($value)`, curried.
 *
 * @category Logic
 */
function not(mixed ...$args): mixed
{
    $_not = static function ($value) {
        return !$value;
    };

    return curry1($_not)(...$args);
}

/**
 * The element at the position (ignoring keys), counted from the end if negative. `null` if out of range.
 * For strings the character at the position, `''` if out of range.
 *
 * Signature: `nth(int $offset, array|string $list)`, curried.
 *
 * @category List
 */
function nth(mixed ...$args): mixed
{
    $_nth = static function (int $offset, array|string $list) {
        if (is_string($list)) {
            return substr($list, $offset, 1);
        }

        $values = array_values($list);
        $index = $offset < 0 ? count($values) + $offset : $offset;

        return $values[$index] ?? null;
    };

    return curry2($_nth)(...$args);
}

/**
 * A copy of the array without the given keys.
 *
 * Signature: `omit(array $keys, array $array)`, curried.
 *
 * @category Object
 */
function omit(mixed ...$args): mixed
{
    $_omit = static function (array $keys, array $list) {
        foreach ($keys as $key) {
            unset($list[$key]);
        }

        return $list;
    };

    return curry2($_omit)(...$args);
}

/**
 * Splits the list into the elements matching the predicate and the others: `[$matching, $others]`.
 * Keys are preserved; the key is passed as second argument if the predicate accepts it.
 *
 * Signature: `partition(callable $predicate, array $list)`, curried.
 *
 * @category List
 */
function partition(mixed ...$args): mixed
{
    $_partition = static function (callable $predicate, array $list) {
        $withKey = _acceptsArgs($predicate, 2);
        $matching = [];
        $others = [];
        foreach ($list as $key => $value) {
            if ($withKey ? $predicate($value, $key) : $predicate($value)) {
                $matching[$key] = $value;
            } else {
                $others[$key] = $value;
            }
        }

        return [$matching, $others];
    };

    return curry2($_partition)(...$args);
}

/**
 * The nested value at the path, `null` if any key on the way is missing.
 *
 * Signature: `path(array $path, $array)`, curried.
 *
 * @category Object
 * @example  path(['address', 'city'], $user);
 */
function path(mixed ...$args): mixed
{
    $_path = static function (array $pathArray, $obj) {
        return paths([$pathArray], $obj)[0];
    };

    return curry2($_path)(...$args);
}

/**
 * Whether the nested value equals the given value (in `equals()` terms). Argument order like Ramda >= 0.29:
 * value first.
 *
 * Signature: `pathEq($value, array $pathArray, $item): bool`, curried.
 *
 * @category Relation
 * @example  pathEq('Berlin', ['address', 'city'], $user);
 */
function pathEq(mixed ...$args): mixed
{
    $_pathEq = static function ($value, array $pathArray, $item): bool {
        return equals(path($pathArray, $item), $value) === true;
    };

    return curry3($_pathEq)(...$args);
}

/**
 * The nested value, or the default if it is missing or `null`.
 *
 * Signature: `pathOr($default, array $pathArray, $item)`, curried.
 *
 * @category Object
 */
function pathOr(mixed ...$args): mixed
{
    $_pathOr = static function ($default, array $pathArray, $item) {
        return path($pathArray, $item) ?? $default;
    };

    return curry3($_pathOr)(...$args);
}

/**
 * Several `path()` lookups at once, returns their values as a list.
 *
 * Signature: `paths(array $paths, $array)`, curried.
 *
 * @category Object
 */
function paths(mixed ...$args): mixed
{
    $_paths = static function (array $pathsArray, $obj) {
        return array_map(
            static function ($paths) use ($obj) {
                $val = $obj;
                foreach ($paths as $path) {
                    if (!is_array($val) || !array_key_exists($path, $val)) {
                        return null;
                    }

                    $val = $val[$path];
                }

                return $val;
            },
            $pathsArray,
        );
    };

    return curry2($_paths)(...$args);
}

/**
 * Whether the nested value satisfies the predicate.
 *
 * Signature: `pathSatisfies(callable $predicate, array $pathArray, $item): bool`, curried.
 *
 * @category Logic
 * @example  pathSatisfies(isEmpty(), ['address', 'zip']);
 */
function pathSatisfies(mixed ...$args): mixed
{
    $_pathSatisfies = static function (callable $predicate, array $pathArray, $item): bool {
        return (bool) $predicate(path($pathArray, $item));
    };

    return curry3($_pathSatisfies)(...$args);
}

/**
 * A copy of the array with the given keys only, in the order of the keys. Missing keys are ignored.
 *
 * Signature: `pick(array $keys, array $array)`, curried.
 *
 * @category Object
 */
function pick(mixed ...$args): mixed
{
    $_pick = static function (array $keys, array $array) {
        $result = [];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                continue;
            }

            $result[$key] = $array[$key];
        }

        return $result;
    };

    return curry2($_pick)(...$args);
}

/**
 * Left-to-right function composition. The first function may have any arity, the others must be unary. The result
 * has the arity of the first function. Throws InvalidArgumentException without functions.
 *
 * @category Function
 */
function pipe(mixed ...$args): mixed
{
    if ($args === []) {
        throw new InvalidArgumentException('pipe requires at least one argument');
    }

    return _arity(
        _getArity($args[0]),
        reduce('\Fnc\_pipe', $args[0], tail($args)),
    );
}

/**
 * The given key (or public property) of each element, see `prop()`. Keys are preserved.
 *
 * Signature: `pluck(string|int $key, array $list)`, curried.
 *
 * @category List
 * @example  pluck('name', [['name' => 'Ada'], ['name' => 'Linus']]); // ['Ada', 'Linus']
 */
function pluck(mixed ...$args): mixed
{
    $_pluck = static function (string|int $key, array $list) {
        return array_map(static fn ($item) => prop($key, $item), $list);
    };

    return curry2($_pluck)(...$args);
}

/**
 * The value of an array key or of a public object property, `null` if it doesn't exist. Works on lists as well.
 *
 * Signature: `prop($key, $item)`, curried.
 *
 * @category Object
 * @example  prop('x', ["x" => 100]); // 100
 * @example  prop('0', [100]); // 100
 */
function prop(mixed ...$args): mixed
{
    $_prop = static function ($key, $item) {
        if (is_array($item)) {
            if (array_key_exists($key, $item)) {
                return $item[$key];
            }
        }

        if (is_object($item)) {
            $vars = get_object_vars($item);
            if (array_key_exists($key, $vars)) {
                return $vars[$key];
            }
        }

        return null;
    };

    return curry2($_prop)(...$args);
}

/**
 * Whether the property equals the given value (in `equals()` terms). Argument order like Ramda >= 0.29:
 * value first.
 *
 * Signature: `propEq($value, string|int $key, $item): bool`, curried.
 *
 * @category Relation
 * @example  filter(propEq('active', 'status'), $users);
 */
function propEq(mixed ...$args): mixed
{
    $_propEq = static function ($value, string|int $key, $item): bool {
        return equals(prop($key, $item), $value) === true;
    };

    return curry3($_propEq)(...$args);
}

/**
 * The property, or the default if it is missing or `null`.
 *
 * Signature: `propOr($default, string|int $key, $item)`, curried.
 *
 * @category Object
 */
function propOr(mixed ...$args): mixed
{
    $_propOr = static function ($default, string|int $key, $item) {
        return prop($key, $item) ?? $default;
    };

    return curry3($_propOr)(...$args);
}

/**
 * Whether the property satisfies the predicate.
 *
 * Signature: `propSatisfies(callable $predicate, string|int $key, $item): bool`, curried.
 *
 * @category Logic
 * @example  ifElse(propSatisfies(isEmpty(), 'rim_brand_id'), always(null), applySpec([...]));
 */
function propSatisfies(mixed ...$args): mixed
{
    $_propSatisfies = static function (callable $predicate, string|int $key, $item): bool {
        return (bool) $predicate(prop($key, $item));
    };

    return curry3($_propSatisfies)(...$args);
}

/**
 * Returns a single item by iterating through the list, successively calling the iterator
 * function and passing it an accumulator value and the current value from the array, and
 * then passing the result to the next call.
 *
 * The iterator function receives (accumulator, value) and, if it accepts a third argument, the key.
 *
 * Signature: `reduce(callable $fn, $acc, array $list)`, curried.
 *
 * @category List
 */
function reduce(mixed ...$args): mixed
{
    $_reduce = static function (callable $fn, $acc, array $list) {
        $withKey = _acceptsArgs($fn, 3);
        foreach ($list as $key => $value) {
            $acc = $withKey ? $fn($acc, $value, $key) : $fn($acc, $value);
        }

        return $acc;
    };

    return curry3($_reduce)(...$args);
}

/**
 * The complement of `filter()`: keeps the elements not matching the predicate. Keys are preserved; the key is
 * passed as second argument if the predicate accepts it.
 *
 * Signature: `reject(callable $predicate, array $list)`, curried.
 *
 * @category List
 */
function reject(mixed ...$args): mixed
{
    $_reject = static function (callable $predicate, array $list) {
        return partition($predicate, $list)[1];
    };

    return curry2($_reject)(...$args);
}

/**
 * Not part of Ramda, specific to this library.
 *
 * Restructures a flat record, e.g. a database row, with a spec like `applySpec()`, but:
 *
 * - a string in the spec is a key of the record: its value is moved there, and the key is removed from the
 *   record (missing keys give `null`),
 * - arrays are nested specs,
 * - callables are called with the whole record and don't remove anything. Strings are always keys, so pass named
 *   functions as closures (`trim(...)`), and wrap array callables (`[$object, 'method']`) in a closure.
 *
 * All other keys of the record are kept. Keys of the spec win over remaining record keys.
 *
 * Signature: `remap(array $spec, array $record)`, curried.
 *
 * @category Object
 * @example  remap(['wheelhotel' => ['id' => 'wheelhotel_id', 'name' => 'wheelhotel_name']], $record);
 */
function remap(mixed ...$args): mixed
{
    $_remap = static function (array $spec, array $record) {
        $moved = [];
        $build = static function (array $spec) use (&$build, &$moved, $record): array {
            $result = [];
            foreach ($spec as $key => $value) {
                if (is_string($value)) {
                    $result[$key] = $record[$value] ?? null;
                    $moved[$value] = true;
                } elseif (is_array($value)) {
                    $result[$key] = $build($value);
                } elseif (is_callable($value)) {
                    $result[$key] = $value($record);
                } else {
                    throw new InvalidArgumentException('remap spec values must be strings, arrays or callables');
                }
            }

            return $result;
        };

        $result = $build($spec);

        return $result + array_diff_key($record, $moved);
    };

    return curry2($_remap)(...$args);
}

/**
 * Reverses the given list
 *
 * Signature: `reverse(array $list)`, curried.
 *
 * @category List
 */
function reverse(mixed ...$args): mixed
{
    $_reverse = static function (array $list) {
        return array_reverse($list);
    };

    return curry1($_reverse)(...$args);
}

/**
 * The elements or characters from `$from` (inclusive) to `$to` (exclusive), like Ramda's slice. Negative
 * indices count from the end, `null` as `$to` means "until the end". Lists keep string keys like `array_slice()`.
 *
 * Signature: `slice(int $from, int|null $to, array|string $list)`, curried.
 *
 * @category List
 * @example  slice(11, 19, '1753-01-01 08:15:00.000'); // '08:15:00'
 */
function slice(mixed ...$args): mixed
{
    $_slice = static function (int $from, int|null $to, array|string $list) {
        $length = is_string($list) ? strlen($list) : count($list);
        $start = $from < 0 ? phpMax($length + $from, 0) : phpMin($from, $length);
        $end = $to === null ? $length : ($to < 0 ? phpMax($length + $to, 0) : phpMin($to, $length));
        $count = phpMax($end - $start, 0);

        return is_string($list) ? substr($list, $start, $count) : array_slice($list, $start, $count);
    };

    return curry3($_slice)(...$args);
}

/**
 * Sorts the list by the values the function returns, ascending (stable, compared with `<=>`). Returns a list.
 *
 * Signature: `sortBy(callable $fn, array $list)`, curried.
 *
 * @category List
 * @example  sortBy(prop('age'), $users);
 */
function sortBy(mixed ...$args): mixed
{
    $_sortBy = static function (callable $fn, array $list) {
        $decorated = array_map(static fn ($value) => [$fn($value), $value], array_values($list));
        usort($decorated, static fn (array $a, array $b) => $a[0] <=> $b[0]);

        return array_column($decorated, 1);
    };

    return curry2($_sortBy)(...$args);
}

/**
 * Sorts the list by the comparators: the first one decides, the next ones break ties (stable). Use with
 * `ascend()` and `descend()`. Returns a list.
 *
 * Signature: `sortWith(array $comparators, array $list)`, curried.
 *
 * @category List
 * @example  sortWith([ascend(prop('carhouse_id')), ascend(prop('id'))], $records);
 */
function sortWith(mixed ...$args): mixed
{
    $_sortWith = static function (array $comparators, array $list) {
        $list = array_values($list);
        usort($list, static function ($a, $b) use ($comparators): int {
            foreach ($comparators as $comparator) {
                $result = $comparator($a, $b);
                if ($result !== 0) {
                    return $result;
                }
            }

            return 0;
        });

        return $list;
    };

    return curry2($_sortWith)(...$args);
}

/**
 * Splits a string into an array of strings based on the given separator.
 *
 * Signature: `split(string $separator, string $list)`, curried.
 *
 * @category List
 */
function split(mixed ...$args): mixed
{
    $_split = static function (string $separator, string $list) {
        if ($separator === '') {
            throw new InvalidArgumentException('split requires a non-empty separator');
        }

        return explode($separator, $list);
    };

    return curry2($_split)(...$args);
}

/**
 * `$a - $b`. `null` counts as 0.
 *
 * Signature: `subtract($a, $b)`, curried.
 *
 * @category Math
 */
function subtract(mixed ...$args): mixed
{
    $_subtract = static function ($a, $b) {
        return ($a ?? 0) - ($b ?? 0);
    };

    return curry2($_subtract)(...$args);
}

/**
 * Adds together all the elements of a list. Returns 0 for an empty list.
 *
 * Signature: `sum(array $elements)`, curried.
 */
function sum(mixed ...$args): mixed
{
    $_sum = static function (array $elements) {
        return array_sum($elements);
    };

    return curry1($_sum)(...$args);
}

/**
 * All but the first element of a list (string keys are preserved, numeric keys renumbered like
 * `array_shift()`) or all but the first character of a string.
 *
 * Signature: `tail(array|string $list)`, curried.
 *
 * @category List
 */
function tail(mixed ...$args): mixed
{
    $_tail = static function (array|string $list) {
        if (is_string($list)) {
            return substr($list, 1);
        }

        array_shift($list);

        return $list;
    };

    return curry1($_tail)(...$args);
}

/**
 * The first `$count` elements of a list (like `array_slice()`: string keys are kept, numeric keys renumbered) or
 * characters of a string.
 *
 * Signature: `take($count, $list)`, curried.
 *
 * @category List
 */
function take(mixed ...$args): mixed
{
    $_take = static function ($count, $list) {
        if (is_array($list)) {
            return array_slice($list, 0, $count);
        }

        return substr($list, 0, $count);
    };

    return curry2($_take)(...$args);
}

/**
 * Calls the function with the value and returns the value. Useful for debugging inside `pipe()`.
 *
 * Signature: `tap(callable $fn, $value)`, curried.
 *
 * @category Function
 * @example  pipe(map($fn), tap('var_dump'), filter($predicate));
 */
function tap(mixed ...$args): mixed
{
    $_tap = static function (callable $fn, $value) {
        $fn($value);

        return $value;
    };

    return curry2($_tap)(...$args);
}

/**
 * Takes a function, which takes a single array argument, and returns
 * a function which:
 *
 *   - takes any number of positional arguments;
 *   - passes these arguments to `fn` as an array; and
 *   - returns the result.
 *
 * @category Function
 */
function unapply(callable $fn): callable
{
    return static function (...$args) use ($fn) {
        return $fn($args);
    };
}

/**
 * The list without duplicates (in `equals()` terms), keeping the first occurrence. Returns a list.
 * Compares each element with all kept ones (quadratic), so use array_unique() for large lists of scalars.
 *
 * Signature: `uniq(array $list)`, curried.
 *
 * @category List
 */
function uniq(mixed ...$args): mixed
{
    $_uniq = static function (array $list) {
        $result = [];
        foreach ($list as $value) {
            foreach ($result as $existing) {
                if (equals($existing, $value)) {
                    continue 2;
                }
            }

            $result[] = $value;
        }

        return $result;
    };

    return curry1($_uniq)(...$args);
}

/**
 * Returns the value unchanged if the predicate is satisfied, otherwise the result of the function.
 *
 * Signature: `unless(callable $predicate, callable $fn, $value)`, curried.
 *
 * @category Logic
 * @example  unless(isNil(), 'trim'); // trims strings, keeps null
 */
function unless(mixed ...$args): mixed
{
    $_unless = static function (callable $predicate, callable $fn, $value) {
        return $predicate($value) ? $value : $fn($value);
    };

    return curry3($_unless)(...$args);
}

/**
 * The values of an array as list, or the values of an object's public properties.
 *
 * Signature: `values(array|object $item)`, curried.
 *
 * @category Object
 */
function values(mixed ...$args): mixed
{
    $_values = static function (array|object $item) {
        return array_values(is_object($item) ? get_object_vars($item) : $item);
    };

    return curry1($_values)(...$args);
}

/**
 * Returns the result of the function if the predicate is satisfied, otherwise the value unchanged.
 *
 * Signature: `when(callable $predicate, callable $fn, $value)`, curried.
 *
 * @category Logic
 * @example  when(is_string(...), 'trim');
 */
function when(mixed ...$args): mixed
{
    $_when = static function (callable $predicate, callable $fn, $value) {
        return $predicate($value) ? $fn($value) : $value;
    };

    return curry3($_when)(...$args);
}

/**
 * How many arguments the provided callable expects (arity): the number of its required parameters, like
 * Function.length in JavaScript. Optional and variadic parameters don't count, so currying a callable with optional
 * parameters needs an explicit arity, e.g. curryN(2, $fn).
 *
 * @throws RuntimeException
 */
function _getArity(mixed $callable): int
{
    if ($callable instanceof Closure) {
        $arity = _curriedArity($callable);
        if ($arity !== null) {
            return $arity;
        }
    }

    return _reflectCallable($callable)->getNumberOfRequiredParameters();
}

/**
 * Remembers (with $arity) or looks up (without) the arity of a curried closure. Curried closures read their
 * arguments via func_get_args() and therefore declare no parameters, so reflection can't tell their arity.
 */
function _curriedArity(Closure $fn, int|null $arity = null): int|null
{
    /** @var WeakMap<Closure, int> $arities */
    static $arities = new WeakMap();

    if ($arity !== null) {
        $arities[$fn] = $arity;

        return $arity;
    }

    return $arities[$fn] ?? null;
}

/**
 * Whether the callable can safely be called with $n arguments without dropping or rejecting them.
 * Internal functions only count their required parameters, so optional parameters (like trim()'s $characters)
 * don't receive unrelated values.
 *
 * @throws RuntimeException
 */
function _acceptsArgs(callable $callable, int $n): bool
{
    // curried closures read their arguments via func_get_args() and declare no parameters
    if ($callable instanceof Closure) {
        $arity = _curriedArity($callable);
        if ($arity !== null) {
            return $arity >= $n;
        }
    }

    $r = _reflectCallable($callable);

    if ($r->isVariadic()) {
        return true;
    }

    if ($r->isInternal()) {
        return $r->getNumberOfRequiredParameters() >= $n;
    }

    return $r->getNumberOfParameters() >= $n;
}

/** @throws RuntimeException */
function _reflectCallable(mixed $callable): ReflectionFunction
{
    if (!is_callable($callable)) {
        throw new RuntimeException('Could not examine callback');
    }

    return new ReflectionFunction(Closure::fromCallable($callable));
}

/**
 * Create function with given arity
 *
 * @throws RuntimeException
 */
function _arity(int $n, callable $fn): callable
{
    // phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter

    switch ($n) {
        case 0:
            return static fn () => $fn(...func_get_args());

        case 1:
            return static fn ($a0) => $fn(...func_get_args());

        case 2:
            return static fn ($a0, $a1) => $fn(...func_get_args());

        case 3:
            return static fn ($a0, $a1, $a2) => $fn(...func_get_args());

        case 4:
            return static fn ($a0, $a1, $a2, $a3) => $fn(...func_get_args());

        case 5:
            return static fn ($a0, $a1, $a2, $a3, $a4) => $fn(...func_get_args());

        case 6:
            return static fn ($a0, $a1, $a2, $a3, $a4, $a5) => $fn(...func_get_args());

        case 7:
            return static fn ($a0, $a1, $a2, $a3, $a4, $a5, $a6) => $fn(...func_get_args());

        case 8:
            return static fn ($a0, $a1, $a2, $a3, $a4, $a5, $a6, $a7) => $fn(...func_get_args());

        case 9:
            return static fn ($a0, $a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8) => $fn(...func_get_args());

        case 10:
            return static fn ($a0, $a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9) => $fn(...func_get_args());

        default:
            throw new RuntimeException(
                'First argument to _arity must be a non-negative integer no greater than ten',
            );
    }

    // phpcs:enable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
}

function _pipe(callable $f, callable $g): callable
{
    return static fn (...$args) => $g($f(...$args));
}
