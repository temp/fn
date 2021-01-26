<?php

declare(strict_types=1);

namespace Fnc;

use Closure;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionObject;
use RuntimeException;
use stdClass;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function array_reduce;
use function array_reverse;
use function array_shift;
use function array_slice;
use function array_sum;
use function call_user_func_array;
use function count;
use function explode;
use function func_get_args;
use function func_num_args;
use function implode;
use function in_array;
use function is_a;
use function is_array;
use function is_bool;
use function is_callable;
use function is_float;
use function is_int;
use function is_numeric;
use function is_object;
use function is_string;
use function max;
use function property_exists;
use function Safe\substr;
use function strpos;
use function trigger_error;
use const ARRAY_FILTER_USE_BOTH;
use const E_USER_DEPRECATED;

// phpcs:disable BrainbitsCodingStandard.Exceptions.GlobalException.GlobalException
// phpcs:disable SlevomatCodingStandard.Commenting.ForbiddenAnnotations.AnnotationForbidden
// phpcs:disable Squiz.Functions.GlobalFunction.Found

/**
 * @param mixed $args
 *
 * @return bool|callable
 */
function all(...$args)
{
    /**
     * Returns `true` if all elements of the list match the predicate, `false` if there are any
     * that don't.
     * Function "breaks early"
     *
     * @category List
     *
     * @param callable $fn   The predicate function.
     * @param mixed[]  $list The array to consider.
     *
     * @return bool `true` if the predicate is satisfied by every element, `false` otherwise.
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

/**
 * @param mixed $arg
 */
function always($arg): callable
{
    /**
     * Returns a function that always returns the given value. Note that for non-primitives the value returned is
     * a reference to the original value.
     *
     * @category Function
     *
     * @return mixed
     */
    return static function () use ($arg) {
        return $arg;
    };
}

/**
 * @param mixed $args
 *
 * @return bool|callable
 */
function any(...$args)
{
    /**
     * Returns `true` if at least one of the elements of the list match the predicate, `false` otherwise.
     * Function "breaks early"
     *
     * @category List
     *
     * @param callable $fn   The predicate function.
     * @param mixed[]  $list The array to consider.
     *
     * @return bool `true` if the predicate is satisfied by at least one element, `false` otherwise.
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

/**
 * @param mixed $args
 *
 * @return mixed|callable
 */
function apply(...$args)
{
    /**
     * Applies function fn to the argument list args. This is useful for creating a fixed-arity function from a
     * variadic function.
     *
     * @category Function
     *
     * @param callable $callable
     * @param mixed[]  $list
     *
     * @return mixed
     */
    $_apply = static function (callable $callable, array $list) {
        return $callable(...$list);
    };

    return curry2($_apply)(...$args);
}

/**
 * @param mixed $spec
 *
 * @return mixed|callable
 */
function applySpec($spec)
{
    $_applySpec = static function (array $spec) {
        $mapValues = static function (callable $fn, $obj) {
            return array_reduce(
                array_keys($obj),
                static function ($acc, $key) use ($fn, $obj) {
                    $acc[$key] = $fn($obj[$key]);

                    return $acc;
                },
                []
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
            $arity = max($arity, _getArity($f));
        }

        return curryN(
            $arity,
            static function (...$args) use ($spec, $mapValues) {
                return $mapValues(
                    static function ($f) use ($args) {
                        return $f(...$args);
                    },
                    $spec
                );
            }
        );
    };

    return curry1($_applySpec)($spec);
}

/**
 * @deprecated Use applySpec()
 *
 * @param mixed $args
 *
 * @return callable|mixed
 */
function arr(...$args)
{
    @trigger_error('arr() is deprecated, use applySpec()', E_USER_DEPRECATED);

    return applySpec(...$args);
}

/**
 * Performs right-to-left function composition. The rightmost function may have any arity; the remaining functions
 * must be unary.
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
    return pipe(...reverse($args));
}

/**
 * @param mixed $args
 *
 * @return mixed|callable
 */
function concat(...$args)
{
    /**
     * Returns a new array consisting of the elements of the first list followed by the elements
     * of the second.
     * Works with associative arrays. In case of key conflict, latest value prevents when you use toArray
     * Still, you need to be mindful about this behavior in a lazy evaluation context
     *
     * @category List
     *
     * @param mixed[] $a
     * @param mixed[] $b
     *
     * @return mixed[]
     */
    $_concat = static function (array $a, array $b) {
        return array_merge($a, $b);
    };

    return curry2($_concat)(...$args);
}

/**
 * @param mixed $args
 *
 * @return bool|callable
 */
function contains(...$args)
{
    /**
     * Returns true if the specified value is equal, in R.equals terms, to at least one element of the given list;
     * false otherwise. Works also with strings.
     *
     * @category List
     *
     * @param mixed   $needle
     * @param mixed[] $list
     *
     * @return bool
     */
    $_contains = static function ($needle, array $list) {
        return in_array($needle, $list, true);
    };

    return curry2($_contains)(...$args);
}

/**
 * @param mixed $args
 */
function converge(...$args): callable
{
    $_converge = static function (callable $fn, array $funcs) {
        return static function (...$args) use ($fn, $funcs) {
            $flipped_apply = flip(apply(), 2);
            $applyItemsOverFunctions = map($flipped_apply($args));

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

    return $_curry4;
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
        default:
            throw new RuntimeException('unsupported arity '.$arity);
    }
}

/**
 * @param mixed $args
 *
 * @return mixed|callable
 */
function divide(...$args)
{
    /**
     * Divides two numbers. Equivalent to `$a / $b`.
     *
     * @param int|float $a The first value.
     * @param int|float $b The second value.
     *
     * @return float The result of `$a / $b`.
     */
    $_divide = static function ($a, $b) {
        if ($a === null) {
            return null;
        }

        if (!$b) {
            return null;
        }

        return $a / $b;
    };

    return curry2($_divide)(...$args);
}

/**
 * @param mixed $args
 *
 * @return callable|mixed
 */
function drop(...$args)
{
    /**
     * Removes the first n elements of the given list
     * Associative array support: For each item, if the key is non-integer, it returns [$key=>value]
     *
     * @category List
     *
     * @param int          $count
     * @param array|string $list
     *
     * @return mixed[]|string
     */
    $_drop = static function ($count, $list) {
        if (is_array($list)) {
            return array_slice($list, $count);
        }

        return substr($list, $count);
    };

    return curry2($_drop)(...$args);
}

/**
 * Returns the empty value of its argument's type.
 *
 * @category Function
 *
 * @param mixed $args
 *
 * @return callable|mixed
 */
function emptyVal(...$args)
{
    $_emptyVal = static function ($value) {
        if (is_object($value)) {
            return new stdClass();
        }
        if (is_array($value)) {
            return [];
        }
        if (is_callable($value)) {
            return static function (): void {
            };
        }
        if (is_string($value)) {
            return '';
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
 * Returns the empty value of its argument's type.
 *
 * @category Function
 *
 * @param mixed $args
 *
 * @return callable|bool
 */
function equals(...$args)
{
    $compare = static function ($actual, $expected) use (&$compare): bool {
        if (is_object($actual)) {
            $actual = (array) $actual;
        }
        if (is_object($expected)) {
            $expected = (array) $expected;
        }
        if (!is_array($expected) || !is_array($actual)) {
            return $actual === $expected;
        }

        foreach ($expected as $key => $value) {
            if (is_numeric($key)) {
                $match = false;
                foreach ($actual as $otherKey => $otherValue) {
                    if (is_numeric($otherKey) && $compare($value, $otherValue)) {
                        $match = true;
                        break;
                    }
                }

                if (!$match) {
                    return false;
                }
            } elseif (!array_key_exists($key, $actual) || !$compare($actual[$key], $expected[$key])) {
                return false;
            }
        }

        foreach ($actual as $key => $value) {
            if (is_numeric($key)) {
                $match = false;
                foreach ($expected as $otherKey => $otherValue) {
                    if (is_numeric($otherKey) && $compare($value, $otherValue)) {
                        $match = true;
                        break;
                    }
                }

                if (!$match) {
                    return false;
                }
            } elseif (!array_key_exists($key, $expected) || !$compare($actual[$key], $expected[$key])) {
                return false;
            }
        }

        return true;
    };

    $_equals = static function ($a, $b) use ($compare) {
        return $compare($a, $b);
    };

    return curry2($_equals)(...$args);
}

/**
 * Returns a new function much like the supplied one, except that the first two arguments' order is reversed.
 *
 * @category Function
 *
 * @param mixed $args
 *
 * @return callable|mixed
 */
function filter(...$args)
{
    /**
     * Returns a new list containing only those items that match a given predicate function. The predicate function is
     * passed one argument: (value).
     *
     * @category List
     *
     * @param callable $callable (value, key)
     * @param mixed[]  $list
     *
     * @return mixed[]
     */
    $_filter = static function (callable $callable, array $list) {
        return array_filter($list, $callable, ARRAY_FILTER_USE_BOTH);
    };

    return curry2($_filter)(...$args);
}

/**
 * Returns a new function much like the supplied one, except that the first two arguments' order is reversed.
 *
 * @category Function
 *
 * @return callable
 */
function flip(callable $callable, ?int $arity = null) // phpcs:ignore
{
    if ($arity === null) {
        $arity = _getArity($callable);
    }

    return curryN($arity, static function () use ($callable) {
        $args = func_get_args();

        return apply($callable, concat([$args[1], $args[0]], tail(tail($args))));
    });
}

/**
 * Returns the first element of a list, NULL is empty list
 * If the list is an associative array, it will return an array with 1 key=>value
 *
 * @category List
 *
 * @param mixed[] $list
 *
 * @return mixed
 */
function head(array $list)
{
    if (!count($list)) {
        return null;
    }

    return array_shift($list);
}

/**
 * @param mixed $args
 *
 * @return callable|mixed
 */
function identity(...$args)
{
    $_identity = static function ($identity) {
        return $identity;
    };

    return curry1($_identity)(...$args);
}

/**
 * @param mixed $args
 *
 * @return callable|mixed
 */
function ifElse(...$args): callable
{
    $_ifElse = static function (callable $condition, ?callable $onTrue, ?callable $onFalse) {
        $__ifElse = static function (...$args) use ($condition, $onTrue, $onFalse) {
            return $condition(...$args) ? $onTrue(...$args) :  $onFalse(...$args);
        };

        return curryN(
            max(
                $condition ? _getArity($condition) : 0,
                $onTrue ? _getArity($onTrue) : 0,
                $onFalse ? _getArity($onFalse) : 0
            ),
            $__ifElse,
        );
    };

    return curry3($_ifElse)(...$args);
}

/**
 * @param mixed $args
 *
 * @return callable|bool
 */
function isEmpty(...$args)
{
    $_isEmpty = static function ($value): bool {
        return equals($value, emptyVal($value));
    };

    return curry1($_isEmpty)(...$args);
}

/**
 * @param mixed $args
 *
 * @return callable|mixed
 */
function join(...$args)
{
    /**
     * Returns a string made by inserting the separator between each element and concatenating all the elements into a
     * single string.
     *
     * @category List
     *
     * @param string  $separator
     * @param mixed[] $list
     *
     * @return mixed
     */
    $_join = static function (string $separator, array $list) {
        return implode($separator, $list);
    };

    return curry2($_join)(...$args);
}

/**
 * @param mixed $args
 *
 * @return callable|mixed
 */
function map(...$args)
{
    /**
     * Returns a new list, constructed by applying the supplied function to every element of the supplied list.
     * The supplied function takes the current item and its index position as arguments
     *
     * @param callable $callable (value, key)
     * @param mixed[]  $list
     *
     * @category List
     *
     * @return mixed[]
     */
    $_map = static function (callable $fn, array $list) {
        return array_map($fn, $list);
    };

    return curry2($_map)(...$args);
}

/**
 * @param mixed $args
 *
 * @return mixed|callable
 */
function merge(...$args)
{
    /**
     * Curried version of array_merge - 2 arrays
     *
     * @category List
     *
     * @param mixed[] $a
     * @param mixed[] $b
     *
     * @return mixed[]
     */
    $_merge = static function (array $a, array $b) {
        return array_merge($a, $b);
    };

    return curry2($_merge)(...$args);
}

/**
 * @param mixed $args
 *
 * @return mixed|callable
 */
function multiply(...$args)
{
    /**
     * Multiplies two numbers. Equivalent to `a * b` but curried.
     *
     * @param int|float $a The first value.
     * @param int|float $b The second value.
     *
     * @return float The result of `$a / $b`.
     */
    $_multiply = static function ($a, $b) {
        if (!$a) {
            return 0;
        }
        if (!$b) {
            return 0;
        }

        return $a * $b;
    };

    return curry2($_multiply)(...$args);
}

/**
 * @param mixed $args
 *
 * @return callable|bool
 */
function none(...$args)
{
    /**
     * Returns `true` if no elements of the list match the predicate, `false` otherwise.
     * Function "breaks early"
     *
     * @category List
     *
     * @param callable $fn   The predicate function.
     * @param mixed[]  $list The array to consider.
     *
     * @return bool `true` if the predicate is not satisfied by every element, `false` otherwise.
     */
    $_none = static function (callable $fn, array $list) {
        return all(_complement($fn), $list);
    };

    return curry2($_none)(...$args);
}

/**
 * @param mixed $args
 *
 * @return callable|bool
 */
function not(...$args)
{
    /**
     * A function that returns the `!` of its argument. It will return `true` when
     * passed false-y value, and `false` when passed a truth-y one.
     *
     * @category Logic
     *
     * @return bool
     */
    $_not = static function ($value) {
        return !$value;
    };

    return curry1($_not)(...$args);
}

/**
 * @param mixed $args
 *
 * @return callable|mixed
 */
function omit(...$args)
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
 * @param mixed $args
 *
 * @return callable|mixed
 */
function path(...$args)
{
    $_path = static function (array $pathArray, $obj) {
        return paths([$pathArray], $obj)[0];
    };

    return curry2($_path)(...$args);
}

/**
 * @param mixed $args
 *
 * @return callable|mixed
 */
function paths(...$args)
{
    $_paths = static function (array $pathsArray, $obj) {
        return array_map(
            static function ($paths) use ($obj) {
                $val = $obj;
                foreach ($paths as $path) {
                    if ($val === null) {
                        return null;
                    }
                    if (!array_key_exists($path, $val)) {
                        return null;
                    }
                    $val = $val[$path];
                }

                return $val;
            },
            $pathsArray
        );
    };

    return curry2($_paths)(...$args);
}

/**
 * @param mixed $args
 *
 * @return callable|mixed
 */
function pipe(...$args)
{
    return _arity(
        _getArity($args[0]),
        reduce('\Fnc\_pipe', $args[0], tail($args))
    );
}

/**
 * @param mixed $args
 *
 * @return callable|mixed
 */
function prop(...$args)
{
    /**
     * Over an object, it returns the indicated property of that object, if it exists or NULL
     * Over an array, it returns the indicated key of that array, if it exists or NULL. Due to how arrays are
     * implemented in PHP, this works on indexed arrays as well
     *
     * @category Object
     *
     * @example  prop('x', ["x" => 100]); // 100
     * @example  prop('0', [100]); // 100
     *
     * @param $key
     * @param $item
     *
     * @return callable|mixed
     */
    $_prop = static function ($key, $item) {
        if (is_array($item)) {
            if (array_key_exists($key, $item)) {
                return $item[$key];
            }
        }

        if (is_object($item)) {
            if (property_exists($item, $key)) {
                return $item->$key;
            }
        }

        return null;
    };

    return curry2($_prop)(...$args);
}

/**
 * @param mixed $args
 *
 * @return callable|mixed
 */
function reduce(...$args)
{
    /**
     * Returns a single item by iterating through the list, successively calling the iterator
     * function and passing it an accumulator value and the current value from the array, and
     * then passing the result to the next call.
     *
     * The iterator function receives three values: (accumulator, value, key)
     *
     * @category List
     *
     * @param callable $fn ($acc, $value)
     * @param mixed    $acc
     * @param mixed[]  $list
     *
     * @return mixed
     */
    $_reduce = static function (callable $fn, $acc, array $list) {
        foreach ($list as $key => $value) {
            $acc = apply($fn, [$acc, $value, $key]);
        }

        return $acc;
    };

    return curry3($_reduce)(...$args);
}

/**
 * @param mixed $args
 *
 * @return callable|mixed
 */
function reverse(...$args)
{
    /**
     * Reverses the given list
     *
     * @category List
     *
     * @return mixed[]
     */
    $_reverse = static function (array $list) {
        return array_reverse($list);
    };

    return curry1($_reverse)(...$args);
}

/**
 * @param mixed $args
 *
 * @return mixed|callable
 */
function sum(...$args)
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
 * @category List
 *
 * @param mixed[] $list
 *
 * @return mixed[]
 */
function tail(array $list): array
{
    if (!count($list)) {
        return [];
    }

    array_shift($list);

    return $list;
}

/**
 * @param mixed $args
 *
 * @return callable|mixed
 */
function take(...$args)
{
    /**
     * Returns the first n elements of the given list
     * Associative array support: For each item, if the key is non-integer, it returns [$key=>value]
     *
     * @category List
     *
     * @param int          $count
     * @param array|string $list
     *
     * @return mixed[]|string
     */
    $_take = static function ($count, $list) {
        if (is_array($list)) {
            return array_slice($list, 0, $count);
        }

        return substr($list, 0, $count);
    };

    return curry2($_take)(...$args);
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
 * How many arguments the provided callable expects (arity)
 *
 * @param callable|mixed $callable
 *
 * @throws RuntimeException
 */
function _getArity($callable): int
{
    $r = false;
    if (is_array($callable)) {
        $r = new ReflectionMethod($callable[0], $callable[1]);
    } elseif (is_string($callable)) {
        if (strpos($callable, '::') !== false) {
            $tmp = explode('::', $callable);
            $r = new ReflectionMethod($tmp[0], $tmp[1]);
        } elseif (is_callable($callable)) {
            $r = new ReflectionFunction($callable);
        }
    } elseif (is_a($callable, Closure::class)) {
        $objR = new ReflectionObject($callable);
        $r = $objR->getMethod('__invoke');
    } elseif (is_object($callable) && is_callable($callable)) {
        $objR = new ReflectionObject($callable);
        $r = $objR->getMethod('__invoke');
    }

    if (!$r) {
        throw new RuntimeException('Could not examine callback');
    }

    return count($r->getParameters());
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
            return static fn() => $fn(...func_get_args());
        case 1:
            return static fn($a0) => $fn(...func_get_args());
        case 2:
            return static fn($a0, $a1) => $fn(...func_get_args());
        case 3:
            return static fn($a0, $a1, $a2) => $fn(...func_get_args());
        case 4:
            return static fn($a0, $a1, $a2, $a3) => $fn(...func_get_args());
        case 5:
            return static fn($a0, $a1, $a2, $a3, $a4) => $fn(...func_get_args());
        case 6:
            return static fn($a0, $a1, $a2, $a3, $a4, $a5) => $fn(...func_get_args());
        case 7:
            return static fn($a0, $a1, $a2, $a3, $a4, $a5, $a6) => $fn(...func_get_args());
        case 8:
            return static fn($a0, $a1, $a2, $a3, $a4, $a5, $a6, $a7) => $fn(...func_get_args());
        case 9:
            return static fn($a0, $a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8) => $fn(...func_get_args());
        case 10:
            return static fn($a0, $a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9) => $fn(...func_get_args());
        default:
            throw new RuntimeException(
                'First argument to _arity must be a non-negative integer no greater than ten'
            );
    }

    // phpcs:enable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
}

function _pipe(callable $f, callable $g): callable
{
    return static fn(...$args) => $g($f(...$args));
}

function _complement(callable $f): callable
{
    return static fn(...$args) => !$f(...$args);
}
