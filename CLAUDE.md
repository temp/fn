# CLAUDE.md

Ramda-inspired functional programming library for PHP (`Fnc` namespace). See README.md for the public API.

## Layout

- `src/functions.php` – the whole library: plain namespaced functions, loaded via Composer `files` autoloading.
  The PSR-4 entry for `Fnc\` exists but there are no classes.
- `tests/Unit/<Function>Test.php` – one test class per public function, `#[CoversFunction('Fnc\…')]`.
  Tests for internal `_foo` helpers are named `Internal<Foo>Test` (phpcs forbids leading underscores in class names).
- `tests/Integration/` – combinations of several functions.
- `examples/` – runnable scripts (`php examples/01-remap.php`). Not part of the test suite, but CI runs all of them
  and fails on any error, warning, notice or deprecation.

## Commands

```bash
vendor/bin/phpunit                     # tests
vendor/bin/phpunit --filter EqualsTest # single test class
vendor/bin/phpstan analyze             # static analysis (level 1)
vendor/bin/phpcs                       # coding standard (brainbits/phpcs-standard), phpcbf to fix
```

CI (`.github/workflows/build.yml`) runs composer validate/audit, phpcs and phpstan on PHP 8.5 and the tests on
PHP 8.4 and 8.5 with lowest and highest dependencies. phpcs only runs with the highest dependencies; the lowest
coding standard version reports different violations.

## How functions are built

Public curried functions follow this pattern; keep it for new functions:

```php
function foo(mixed ...$args): mixed
{
    /** docblock describing the function */
    $_foo = static function (callable $fn, array $list) {
        // implementation
    };

    return curry2($_foo)(...$args);
}
```

- Argument order is "data last" like Ramda.
- `curry1()`…`curry5()` read arguments via `func_get_args()` and declare no parameters, so reflection can't see
  their arity. They register it in `_curriedArity()` (a `WeakMap`); `_getArity()` checks that first.
- `_getArity()` counts **required** parameters only (like JS `Function.length`). It drives currying in
  `applySpec`, `ifElse`, `flip` and `pipe`.
- `_acceptsArgs()` answers a different question: may a callback receive an extra argument (the key in `filter`
  and `reduce`)? Internal PHP functions throw `ArgumentCountError` on surplus arguments, so only pass extra
  arguments when this returns true.
- Functions prefixed with `_` are internal.
- The library defines `Fnc\max` and `Fnc\min`, so PHP's `max()`/`min()` are imported as `phpMax`/`phpMin` in
  `src/functions.php`. A plain `max(...)` call inside the namespace would hit the curried binary `Fnc\max`. The same
  applies to any new function named like a PHP builtin.

## Semantics decisions (don't silently change)

- `equals`: strict like Ramda – list order and length matter, associative key order doesn't, objects need the same
  class, closures compare by identity.
- `divide`: like Ramda – `null` counts as 0, division by zero returns `INF`/`-INF`/`NAN` via `fdiv()`.
- `isEmpty(0)` and `isEmpty(null)` are `true` (differs from Ramda).
- `filter` and `map` preserve keys.
- `ifElse` treats a `null` branch as identity of the first argument.
- `propEq` / `pathEq` take the value first (Ramda >= 0.29 order).
- Curried comparisons take the left operand first: `gt(5)` is "5 > …".
- `sortBy`, `sortWith`, `uniq`, `chain`, `flatten` return lists; `filter`, `reject`, `partition`, `pluck` preserve
  keys.
- `remap` is our own addition, Ramda has no equivalent. Strings in the spec are always record keys (moved and removed), never callables; arrays are nested specs;
  callables get the whole record and remove nothing. Spec keys win over remaining record keys. Built for the
  `converge(merge(), [applySpec(...), omit(...)])` pattern in the consuming projects' SQL read models.

## Conventions

- PHP `^8.4`, `declare(strict_types=1)`, global functions imported with `use function`.
- Every bug fix gets a regression test that fails without the fix.
- Conventional Commits are required: semantic-release (`.releaserc`, `.github/workflows/release.yaml`) creates tags
  without `v` prefix (`0.8.0`) and GitHub releases from commits on `main`. `fix:` → patch, `feat:` → minor.
  Avoid `!` / `BREAKING CHANGE` footers while the API is 0.x – they would trigger 1.0.0.
- `main` is protected by a ruleset: changes go through pull requests with green CI checks.

## Outlook

PHP 8.6 adds partial function application (`foo(?, 'x')`). Together with the pipe operator `|>` (PHP 8.5) it
covers much of what auto-currying does here. Whether a future 1.0 API keeps auto-currying or switches to typed
signatures is still open.
