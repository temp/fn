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

function add(mixed ...$args): mixed
{
    /**
     * Adds two numbers. `null` counts as 0.
     *
     * @category Math
     * @example  add(2, 3); // 5
     */
    $_add = static function ($a, $b) {
        return ($a ?? 0) + ($b ?? 0);
    };

    return curry2($_add)(...$args);
}

function all(mixed ...$args): bool|callable
{
    /**
     * Returns `true` if all elements of the list match the predicate, `false` if there are any
     * that don't.
     * Function "breaks early"
     *
     * @param callable $fn   The predicate function.
     * @param mixed[]  $list The array to consider.
     *
     * @return bool `true` if the predicate is satisfied by every element, `false` otherwise.
     *
     * @category List
     */
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

function allPass(mixed ...$args): mixed
{
    /**
     * Takes a list of predicates and returns a predicate that is satisfied if all of them are satisfied by the
     * given arguments. Stops at the first predicate that isn't satisfied.
     *
     * @category Logic
     */
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

function always(mixed $arg): callable
{
    /**
     * Returns a function that always returns the given value. Note that for non-primitives the value returned is
     * a reference to the original value.
     *
     * @return mixed
     *
     * @category Function
     */
    return static function () use ($arg) {
        return $arg;
    };
}

function any(mixed ...$args): bool|callable
{
    /**
     * Returns `true` if at least one of the elements of the list match the predicate, `false` otherwise.
     * Function "breaks early"
     *
     * @param callable $fn   The predicate function.
     * @param mixed[]  $list The array to consider.
     *
     * @return bool `true` if the predicate is satisfied by at least one element, `false` otherwise.
     *
     * @category List
     */
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

function anyPass(mixed ...$args): mixed
{
    /**
     * Takes a list of predicates and returns a predicate that is satisfied if at least one of them is satisfied by
     * the given arguments. Stops at the first predicate that is satisfied.
     *
     * @category Logic
     */
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

function apply(mixed ...$args): mixed
{
    /**
     * Applies function fn to the argument list args. This is useful for creating a fixed-arity function from a
     * variadic function.
     *
     * @param callable $callable
     * @param mixed[]  $list
     *
     * @return mixed
     *
     * @category Function
     */
    $_apply = static function (callable $callable, array $list) {
        return $callable(...$list);
    };

    return curry2($_apply)(...$args);
}

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

function ascend(mixed ...$args): mixed
{
    /**
     * Comparator for `sortWith()`: compares two values by the result of the function, ascending.
     *
     * @category Function
     * @example  sortWith([ascend(prop('carhouse_id')), descend(prop('date'))], $records);
     */
    $_ascend = static function (callable $fn, $a, $b): int {
        return $fn($a) <=> $fn($b);
    };

    return curry3($_ascend)(...$args);
}

function assoc(mixed ...$args): mixed
{
    /**
     * Returns a copy of the array with the key set to the value.
     *
     * @category Object
     * @example  assoc('b', 2, ['a' => 1]); // ['a' => 1, 'b' => 2]
     */
    $_assoc = static function (string|int $key, $value, array $array) {
        $array[$key] = $value;

        return $array;
    };

    return curry3($_assoc)(...$args);
}

function assocPath(mixed ...$args): mixed
{
    /**
     * Returns a copy of the array with the nested key set to the value. Missing levels are created as arrays, non
     * array values on the way are replaced.
     *
     * @category Object
     * @example  assocPath(['a', 'b'], 1, []); // ['a' => ['b' => 1]]
     */
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

function both(mixed ...$args): mixed
{
    /**
     * Returns a predicate that is satisfied if both predicates are satisfied. The second predicate is only called if
     * the first one is satisfied.
     *
     * @category Logic
     */
    $_both = static function (callable $f, callable $g) {
        return static fn (...$args): bool => $f(...$args) && $g(...$args);
    };

    return curry2($_both)(...$args);
}

function chain(mixed ...$args): mixed
{
    /**
     * Maps each element to a list and concatenates the results (flatMap). Returns a list.
     *
     * @category List
     * @example  chain(fn ($n) => [$n, $n], [1, 2]); // [1, 1, 2, 2]
     */
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
        return not($fn(...$args));
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

function concat(mixed ...$args): mixed
{
    /**
     * Concatenates two lists or two strings. Lists are treated as lists: the result contains the values of `$a`
     * followed by the values of `$b`, keys are not preserved. Use `merge()` for arrays with string keys.
     *
     * @param mixed[]|string $a
     * @param mixed[]|string $b
     *
     * @return mixed[]|string
     *
     * @category List
     * @example  concat([1, 2], [3]); // [1, 2, 3]
     * @example  concat('ab', 'cd'); // 'abcd'
     */
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

function cond(mixed ...$args): mixed
{
    /**
     * Takes a list of [predicate, transformer] pairs and returns a function that calls the transformer of the first
     * pair whose predicate is satisfied. Returns null if no predicate matches; use `always(true)` as the last
     * predicate for a default.
     *
     * @category Logic
     */
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

/** @deprecated Use includes(), which compares with equals() and also works on strings. */
function contains(mixed ...$args): bool|callable
{
    /**
     * Returns true if the list contains the value, compared strictly (`in_array(…, true)`): objects by identity,
     * arrays only if identical including key order.
     *
     * @param mixed   $needle
     * @param mixed[] $list
     *
     * @return bool
     *
     * @category List
     */
    $_contains = static function ($needle, array $list) {
        return in_array($needle, $list, true);
    };

    return curry2($_contains)(...$args);
}

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

function dec(mixed ...$args): mixed
{
    /**
     * Decrements a number by 1.
     *
     * @category Math
     */
    $_dec = static function ($value) {
        return $value - 1;
    };

    return curry1($_dec)(...$args);
}

function defaultTo(mixed ...$args): mixed
{
    /**
     * Returns the value, or the default if the value is `null` or `NAN`.
     *
     * @category Logic
     * @example  defaultTo(42, null); // 42
     */
    $_defaultTo = static function ($default, $value) {
        if ($value === null || (is_float($value) && is_nan($value))) {
            return $default;
        }

        return $value;
    };

    return curry2($_defaultTo)(...$args);
}

function descend(mixed ...$args): mixed
{
    /**
     * Comparator for `sortWith()`: compares two values by the result of the function, descending.
     *
     * @category Function
     */
    $_descend = static function (callable $fn, $a, $b): int {
        return $fn($b) <=> $fn($a);
    };

    return curry3($_descend)(...$args);
}

function divide(mixed ...$args): mixed
{
    /**
     * Divides two numbers. Equivalent to `$a / $b`, like Ramda: `null`, `false` and `''` count as 0, and dividing
     * by zero (also `'0'`) returns INF, -INF or NAN instead of throwing.
     *
     * @param int|float|string|bool|null $a The first value.
     * @param int|float|string|bool|null $b The second value.
     *
     * @return int|float The result of `$a / $b`.
     */
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

function drop(mixed ...$args): mixed
{
    /**
     * Removes the first n elements of the given list
     * Associative array support: For each item, if the key is non-integer, it returns [$key=>value]
     *
     * @param int          $count
     * @param array|string $list
     *
     * @return mixed[]|string
     *
     * @category List
     */
    $_drop = static function ($count, $list) {
        if (is_array($list)) {
            return array_slice($list, $count);
        }

        return substr($list, $count);
    };

    return curry2($_drop)(...$args);
}

function either(mixed ...$args): mixed
{
    /**
     * Returns a predicate that is satisfied if at least one of the predicates is satisfied. The second predicate is
     * only called if the first one isn't satisfied.
     *
     * @category Logic
     */
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
 * Deep equality, see the docblock inside.
 *
 * @category Relation
 */
function equals(mixed ...$args): callable|bool
{
    /**
     * Deep equality: scalars are compared strictly (unlike in Ramda, `1` and `1.0` differ, as PHP has separate int and
     * float types), `NAN` equals `NAN` like in Ramda, arrays by key (list order matters, order of string keys
     * doesn't), objects only if they are of the same class and all their properties are equal. Closures are
     * compared by identity. Cyclic object graphs are supported.
     */
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

function evolve(mixed ...$args): mixed
{
    /**
     * Returns a copy of the array with the transformations applied to the matching keys. A nested array of
     * transformations is applied to the nested array. Keys missing in the input are not added.
     * Callables in arrays (`[$object, 'method']`) would be taken for nested transformations, wrap them in a closure.
     *
     * @category Object
     * @example  evolve(['count' => inc()], ['count' => 1, 'name' => 'a']); // ['count' => 2, 'name' => 'a']
     */
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

function filter(mixed ...$args): mixed
{
    /**
     * Returns a new list containing only those items that match a given predicate function. The predicate function is
     * passed one argument: (value).
     *
     * The key is passed as second argument only if the predicate accepts it.
     *
     * @param callable $callable (value[, key])
     * @param mixed[]  $list
     *
     * @return mixed[]
     *
     * @category List
     */
    $_filter = static function (callable $callable, array $list) {
        if (_acceptsArgs($callable, 2)) {
            return array_filter($list, $callable, ARRAY_FILTER_USE_BOTH);
        }

        return array_filter($list, $callable);
    };

    return curry2($_filter)(...$args);
}

function find(mixed ...$args): mixed
{
    /**
     * Returns the first element matching the predicate, `null` if there is none.
     *
     * @category List
     */
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

function flatten(mixed ...$args): mixed
{
    /**
     * Flattens nested arrays into one list, recursively.
     *
     * @category List
     * @example  flatten([1, [2, [3, 4]]]); // [1, 2, 3, 4]
     */
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

function groupBy(mixed ...$args): mixed
{
    /**
     * Groups the elements by the key the function returns for them. Each group is a list.
     *
     * @category List
     * @example  groupBy(fn ($n) => $n % 2 ? 'odd' : 'even', [1, 2, 3]); // ['odd' => [1, 3], 'even' => [2]]
     */
    $_groupBy = static function (callable $fn, array $list) {
        $groups = [];
        foreach ($list as $value) {
            $groups[$fn($value)][] = $value;
        }

        return $groups;
    };

    return curry2($_groupBy)(...$args);
}

function gt(mixed ...$args): mixed
{
    /**
     * `$a > $b`. Note the argument order when curried: `gt(5)` is "5 is greater than …", use `lt(5)` for
     * "… is greater than 5".
     *
     * @category Relation
     */
    $_gt = static function ($a, $b): bool {
        return $a > $b;
    };

    return curry2($_gt)(...$args);
}

function gte(mixed ...$args): mixed
{
    /**
     * `$a >= $b`.
     *
     * @category Relation
     */
    $_gte = static function ($a, $b): bool {
        return $a >= $b;
    };

    return curry2($_gte)(...$args);
}

function has(mixed ...$args): mixed
{
    /**
     * Whether the array has the key or the object has the public property, even if its value is `null`.
     *
     * @category Object
     */
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
 * Returns the first element of a list, NULL is empty list
 * If the list is an associative array, it will return an array with 1 key=>value
 *
 * @param mixed[] $list
 *
 * @category List
 */
function head(array $list): mixed
{
    if (!count($list)) {
        return null;
    }

    return array_shift($list);
}

function identity(mixed ...$args): mixed
{
    $_identity = static function ($identity) {
        return $identity;
    };

    return curry1($_identity)(...$args);
}

/** @return callable|mixed */
function ifElse(mixed ...$args): callable
{
    /**
     * Creates a function that calls $onTrue if $condition is satisfied, $onFalse otherwise.
     * A null branch returns the first argument unchanged.
     */
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

function inc(mixed ...$args): mixed
{
    /**
     * Increments a number by 1.
     *
     * @category Math
     */
    $_inc = static function ($value) {
        return $value + 1;
    };

    return curry1($_inc)(...$args);
}

function includes(mixed ...$args): mixed
{
    /**
     * Whether the list contains an element equal to the needle (in `equals()` terms), or the string contains the
     * needle as substring.
     *
     * @category List
     * @example  includes(['a' => 1], [['a' => 1], ['b' => 2]]); // true
     * @example  includes('ell', 'hello'); // true
     */
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

function isEmpty(mixed ...$args): callable|bool
{
    $_isEmpty = static function ($value): bool {
        return equals($value, emptyVal($value));
    };

    return curry1($_isEmpty)(...$args);
}

function isNil(mixed ...$args): mixed
{
    /**
     * Whether the value is `null`.
     *
     * @category Type
     */
    $_isNil = static function ($value): bool {
        return $value === null;
    };

    return curry1($_isNil)(...$args);
}

function join(mixed ...$args): mixed
{
    /**
     * Returns a string made by inserting the separator between each element and concatenating all the elements into a
     * single string.
     *
     * @param string  $separator
     * @param mixed[] $list
     *
     * @return mixed
     *
     * @category List
     */
    $_join = static function (string $separator, array $list) {
        return implode($separator, $list);
    };

    return curry2($_join)(...$args);
}

function keys(mixed ...$args): mixed
{
    /**
     * The keys of an array, or the names of an object's public properties.
     *
     * @category Object
     */
    $_keys = static function (array|object $item) {
        return array_keys(is_object($item) ? get_object_vars($item) : $item);
    };

    return curry1($_keys)(...$args);
}

function last(mixed ...$args): mixed
{
    /**
     * The last element of a list (`null` if empty) or the last character of a string.
     *
     * @category List
     */
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

function logicAnd(mixed ...$args): mixed
{
    $_and = static function ($a, $b) {
        return $a && $b;
    };

    return curry2($_and)(...$args);
}

function logicOr(mixed ...$args): mixed
{
    $_or = static function ($a, $b) {
        return $a || $b;
    };

    return curry2($_or)(...$args);
}

function logicXor(mixed ...$args): mixed
{
    $_xor = static function ($a, $b) {
        return $a xor $b;
    };

    return curry2($_xor)(...$args);
}

function lt(mixed ...$args): mixed
{
    /**
     * `$a < $b`. When curried, `lt(5)` reads "5 is less than …", i.e. "… is greater than 5".
     *
     * @category Relation
     */
    $_lt = static function ($a, $b): bool {
        return $a < $b;
    };

    return curry2($_lt)(...$args);
}

function lte(mixed ...$args): mixed
{
    /**
     * `$a <= $b`.
     *
     * @category Relation
     */
    $_lte = static function ($a, $b): bool {
        return $a <= $b;
    };

    return curry2($_lte)(...$args);
}

function map(mixed ...$args): mixed
{
    /**
     * Returns a new list, constructed by applying the supplied function to every element of the supplied list.
     * The supplied function takes the current item and its index position as arguments
     *
     * @param callable $callable (value, key)
     * @param mixed[]  $list
     *
     * @return mixed[]
     *
     * @category List
     */
    $_map = static function (callable $fn, array|null $list) {
        if (!is_array($list)) {
            return [];
        }

        return array_map($fn, $list);
    };

    return curry2($_map)(...$args);
}

function max(mixed ...$args): mixed
{
    /**
     * The larger of two values.
     *
     * @category Relation
     */
    $_max = static function ($a, $b) {
        return $b > $a ? $b : $a;
    };

    return curry2($_max)(...$args);
}

function mean(mixed ...$args): mixed
{
    /**
     * The arithmetic mean of the list, `NAN` for an empty list.
     *
     * @category Math
     */
    $_mean = static function (array $list) {
        return fdiv(array_sum($list), count($list));
    };

    return curry1($_mean)(...$args);
}

function merge(mixed ...$args): mixed
{
    /**
     * Curried array_merge() of two arrays: for string keys the value of $b wins, numeric keys are appended.
     * Use concat() to join lists.
     *
     * @param mixed[] $a
     * @param mixed[] $b
     *
     * @return mixed[]
     *
     * @category List
     */
    $_merge = static function (array $a, array $b) {
        return array_merge($a, $b);
    };

    return curry2($_merge)(...$args);
}

function min(mixed ...$args): mixed
{
    /**
     * The smaller of two values.
     *
     * @category Relation
     */
    $_min = static function ($a, $b) {
        return $b < $a ? $b : $a;
    };

    return curry2($_min)(...$args);
}

function multiply(mixed ...$args): mixed
{
    /**
     * Multiplies two numbers. Equivalent to `a * b` but curried.
     *
     * @param int|float $a The first value.
     * @param int|float $b The second value.
     *
     * @return int|float The result of `$a * $b`.
     */
    $_multiply = static function ($a, $b) {
        // null, false and '' count as 0; numbers keep their type, so multiply(0.0, 1.5) is 0.0
        $orZero = static fn ($value) => $value === null || $value === false || $value === '' ? 0 : $value;

        return $orZero($a) * $orZero($b);
    };

    return curry2($_multiply)(...$args);
}

function none(mixed ...$args): callable|bool
{
    /**
     * Returns `true` if no elements of the list match the predicate, `false` otherwise.
     * Function "breaks early"
     *
     * @param callable $fn   The predicate function.
     * @param mixed[]  $list The array to consider.
     *
     * @return bool `true` if the predicate is not satisfied by every element, `false` otherwise.
     *
     * @category List
     */
    $_none = static function (callable $fn, array $list) {
        return all(complement($fn), $list);
    };

    return curry2($_none)(...$args);
}

function not(mixed ...$args): callable|bool
{
    /**
     * A function that returns the `!` of its argument. It will return `true` when
     * passed false-y value, and `false` when passed a truth-y one.
     *
     * @return bool
     *
     * @category Logic
     */
    $_not = static function ($value) {
        return !$value;
    };

    return curry1($_not)(...$args);
}

function nth(mixed ...$args): mixed
{
    /**
     * The element at the position (ignoring keys), counted from the end if negative. `null` if out of range.
     * For strings the character at the position, `''` if out of range.
     *
     * @category List
     */
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

function partition(mixed ...$args): mixed
{
    /**
     * Splits the list into the elements matching the predicate and the others: `[$matching, $others]`.
     * Keys are preserved; the key is passed as second argument if the predicate accepts it.
     *
     * @category List
     */
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

function path(mixed ...$args): mixed
{
    $_path = static function (array $pathArray, $obj) {
        return paths([$pathArray], $obj)[0];
    };

    return curry2($_path)(...$args);
}

function pathEq(mixed ...$args): mixed
{
    /**
     * Whether the nested value equals the given value (in `equals()` terms). Argument order like Ramda >= 0.29:
     * value first.
     *
     * @category Relation
     * @example  pathEq('Berlin', ['address', 'city'], $user);
     */
    $_pathEq = static function ($value, array $pathArray, $item): bool {
        return equals(path($pathArray, $item), $value);
    };

    return curry3($_pathEq)(...$args);
}

function pathOr(mixed ...$args): mixed
{
    /**
     * The nested value, or the default if it is missing or `null`.
     *
     * @category Object
     */
    $_pathOr = static function ($default, array $pathArray, $item) {
        return path($pathArray, $item) ?? $default;
    };

    return curry3($_pathOr)(...$args);
}

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

function pathSatisfies(mixed ...$args): mixed
{
    /**
     * Whether the nested value satisfies the predicate.
     *
     * @category Logic
     * @example  pathSatisfies(isEmpty(), ['address', 'zip']);
     */
    $_pathSatisfies = static function (callable $predicate, array $pathArray, $item): bool {
        return (bool) $predicate(path($pathArray, $item));
    };

    return curry3($_pathSatisfies)(...$args);
}

function pick(mixed ...$args): mixed
{
    /**
     * A copy of the array with the given keys only, in the order of the keys. Missing keys are ignored.
     *
     * @category Object
     */
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

function pluck(mixed ...$args): mixed
{
    /**
     * The given key (or public property) of each element, see `prop()`. Keys are preserved.
     *
     * @category List
     * @example  pluck('name', [['name' => 'Ada'], ['name' => 'Linus']]); // ['Ada', 'Linus']
     */
    $_pluck = static function (string|int $key, array $list) {
        return array_map(static fn ($item) => prop($key, $item), $list);
    };

    return curry2($_pluck)(...$args);
}

function prop(mixed ...$args): mixed
{
    /**
     * Over an object, it returns the indicated property of that object, if it exists or NULL
     * Over an array, it returns the indicated key of that array, if it exists or NULL. Due to how arrays are
     * implemented in PHP, this works on indexed arrays as well
     *
     * @param $key
     * @param $item
     *
     * @return callable|mixed
     *
     * @category Object
     * @example  prop('x', ["x" => 100]); // 100
     * @example  prop('0', [100]); // 100
     */
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

function propEq(mixed ...$args): mixed
{
    /**
     * Whether the property equals the given value (in `equals()` terms). Argument order like Ramda >= 0.29:
     * value first.
     *
     * @category Relation
     * @example  filter(propEq('active', 'status'), $users);
     */
    $_propEq = static function ($value, string|int $key, $item): bool {
        return equals(prop($key, $item), $value);
    };

    return curry3($_propEq)(...$args);
}

function propOr(mixed ...$args): mixed
{
    /**
     * The property, or the default if it is missing or `null`.
     *
     * @category Object
     */
    $_propOr = static function ($default, string|int $key, $item) {
        return prop($key, $item) ?? $default;
    };

    return curry3($_propOr)(...$args);
}

function propSatisfies(mixed ...$args): mixed
{
    /**
     * Whether the property satisfies the predicate.
     *
     * @category Logic
     * @example  ifElse(propSatisfies(isEmpty(), 'rim_brand_id'), always(null), applySpec([...]));
     */
    $_propSatisfies = static function (callable $predicate, string|int $key, $item): bool {
        return (bool) $predicate(prop($key, $item));
    };

    return curry3($_propSatisfies)(...$args);
}

function reduce(mixed ...$args): mixed
{
    /**
     * Returns a single item by iterating through the list, successively calling the iterator
     * function and passing it an accumulator value and the current value from the array, and
     * then passing the result to the next call.
     *
     * The iterator function receives (accumulator, value) and, if it accepts a third argument, the key.
     *
     * @param callable $fn ($acc, $value[, $key])
     * @param mixed    $acc
     * @param mixed[]  $list
     *
     * @return mixed
     *
     * @category List
     */
    $_reduce = static function (callable $fn, $acc, array $list) {
        $withKey = _acceptsArgs($fn, 3);
        foreach ($list as $key => $value) {
            $acc = $withKey ? $fn($acc, $value, $key) : $fn($acc, $value);
        }

        return $acc;
    };

    return curry3($_reduce)(...$args);
}

function reject(mixed ...$args): mixed
{
    /**
     * The complement of `filter()`: keeps the elements not matching the predicate. Keys are preserved; the key is
     * passed as second argument if the predicate accepts it.
     *
     * @category List
     */
    $_reject = static function (callable $predicate, array $list) {
        return partition($predicate, $list)[1];
    };

    return curry2($_reject)(...$args);
}

function remap(mixed ...$args): mixed
{
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
     * @category Object
     * @example  remap(['wheelhotel' => ['id' => 'wheelhotel_id', 'name' => 'wheelhotel_name']], $record);
     */
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

function reverse(mixed ...$args): mixed
{
    /**
     * Reverses the given list
     *
     * @return mixed[]
     *
     * @category List
     */
    $_reverse = static function (array $list) {
        return array_reverse($list);
    };

    return curry1($_reverse)(...$args);
}

function slice(mixed ...$args): mixed
{
    /**
     * The elements or characters from `$from` (inclusive) to `$to` (exclusive), like Ramda's slice. Negative
     * indices count from the end, `null` as `$to` means "until the end". Lists keep string keys like `array_slice()`.
     *
     * @category List
     * @example  slice(11, 19, '1753-01-01 08:15:00.000'); // '08:15:00'
     */
    $_slice = static function (int $from, int|null $to, array|string $list) {
        $length = is_string($list) ? strlen($list) : count($list);
        $start = $from < 0 ? phpMax($length + $from, 0) : phpMin($from, $length);
        $end = $to === null ? $length : ($to < 0 ? phpMax($length + $to, 0) : phpMin($to, $length));
        $count = phpMax($end - $start, 0);

        return is_string($list) ? substr($list, $start, $count) : array_slice($list, $start, $count);
    };

    return curry3($_slice)(...$args);
}

function sortBy(mixed ...$args): mixed
{
    /**
     * Sorts the list by the values the function returns, ascending (stable, compared with `<=>`). Returns a list.
     *
     * @category List
     * @example  sortBy(prop('age'), $users);
     */
    $_sortBy = static function (callable $fn, array $list) {
        $decorated = array_map(static fn ($value) => [$fn($value), $value], array_values($list));
        usort($decorated, static fn (array $a, array $b) => $a[0] <=> $b[0]);

        return array_column($decorated, 1);
    };

    return curry2($_sortBy)(...$args);
}

function sortWith(mixed ...$args): mixed
{
    /**
     * Sorts the list by the comparators: the first one decides, the next ones break ties (stable). Use with
     * `ascend()` and `descend()`. Returns a list.
     *
     * @category List
     * @example  sortWith([ascend(prop('carhouse_id')), ascend(prop('id'))], $records);
     */
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

function split(mixed ...$args): mixed
{
    /**
     * Splits a string into an array of strings based on the given separator.
     *
     * @param string $separator
     * @param string $list
     *
     * @return mixed[]
     *
     * @category List
     */
    $_split = static function (string $separator, string $list) {
        return explode($separator, $list);
    };

    return curry2($_split)(...$args);
}

function subtract(mixed ...$args): mixed
{
    /**
     * `$a - $b`. `null` counts as 0.
     *
     * @category Math
     */
    $_subtract = static function ($a, $b) {
        return ($a ?? 0) - ($b ?? 0);
    };

    return curry2($_subtract)(...$args);
}

function sum(mixed ...$args): mixed
{
    /**
     * Adds together all the elements of a list. Returns 0 for an empty list.
     *
     * @param int[]|float[] $elements  A list of numbers.
     *
     * @return int|float The sum of all the numbers in the list.
     */
    $_sum = static function (array $elements) {
        return array_sum($elements);
    };

    return curry1($_sum)(...$args);
}

/**
 * Returns all but the first element of a list.
 * Preserves keys on associative (non-numerical key) arrays
 *
 * @param mixed[] $list
 *
 * @return mixed[]
 *
 * @category List
 */
function tail(array $list): array
{
    if (!count($list)) {
        return [];
    }

    array_shift($list);

    return $list;
}

function take(mixed ...$args): mixed
{
    /**
     * Returns the first n elements of the given list
     * Associative array support: For each item, if the key is non-integer, it returns [$key=>value]
     *
     * @param int          $count
     * @param array|string $list
     *
     * @return mixed[]|string
     *
     * @category List
     */
    $_take = static function ($count, $list) {
        if (is_array($list)) {
            return array_slice($list, 0, $count);
        }

        return substr($list, 0, $count);
    };

    return curry2($_take)(...$args);
}

function tap(mixed ...$args): mixed
{
    /**
     * Calls the function with the value and returns the value. Useful for debugging inside `pipe()`.
     *
     * @category Function
     * @example  pipe(map($fn), tap('var_dump'), filter($predicate));
     */
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

function uniq(mixed ...$args): mixed
{
    /**
     * The list without duplicates (in `equals()` terms), keeping the first occurrence. Returns a list.
     * Compares each element with all kept ones (quadratic), so use array_unique() for large lists of scalars.
     *
     * @category List
     */
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

function unless(mixed ...$args): mixed
{
    /**
     * Returns the value unchanged if the predicate is satisfied, otherwise the result of the function.
     *
     * @category Logic
     * @example  unless(isNil(), 'trim'); // trims strings, keeps null
     */
    $_unless = static function (callable $predicate, callable $fn, $value) {
        return $predicate($value) ? $value : $fn($value);
    };

    return curry3($_unless)(...$args);
}

function values(mixed ...$args): mixed
{
    /**
     * The values of an array as list, or the values of an object's public properties.
     *
     * @category Object
     */
    $_values = static function (array|object $item) {
        return array_values(is_object($item) ? get_object_vars($item) : $item);
    };

    return curry1($_values)(...$args);
}

function when(mixed ...$args): mixed
{
    /**
     * Returns the result of the function if the predicate is satisfied, otherwise the value unchanged.
     *
     * @category Logic
     * @example  when(is_string(...), 'trim');
     */
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
