# CLAUDE.md

Ramda-inspired functional programming library for PHP: package `temp/fnc` (until 1.0 `temp/fn`), namespace `Fnc`. See README.md for the public API.

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
vendor/bin/phpstan analyze             # static analysis (level 8)
vendor/bin/phpcs                       # coding standard (brainbits/phpcs-standard), phpcbf to fix
```

CI (`.github/workflows/build.yml`) runs composer validate/audit, phpcs and phpstan on PHP 8.5 and the tests on
PHP 8.4 and 8.5 with lowest and highest dependencies. phpcs only runs with the highest dependencies; the lowest
coding standard version reports different violations.

## How functions are built

Public curried functions follow this pattern; keep it for new functions:

```php
/**
 * What the function does.
 *
 * Signature: `foo(callable $fn, array $list)`, curried.
 *
 * @category List
 * @example  foo($fn, [1, 2]);
 */
function foo(mixed ...$args): mixed
{
    $_foo = static function (callable $fn, array $list) {
        // implementation
    };

    return curry2($_foo)(...$args);
}
```

- The docblock goes above the function so IDEs and phpstan see it. No `@param`/`@return` tags: the real parameters
  are `mixed ...$args`, and a narrower `@return` would be wrong for partial calls (which return a closure).
  Describe the curried parameters in the `Signature:` line instead.

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
  class, closures compare by identity, `NAN` equals `NAN`, cycles are handled. Unlike Ramda, `1` and `1.0` differ.
- `divide`: like Ramda – `null`, `false` and `''` count as 0, division by zero (also `'0'`) returns `INF`/`-INF`/`NAN`
  via `fdiv()`.
- `isEmpty(0)` and `isEmpty(null)` are `true` (differs from Ramda).
- `filter` and `map` preserve keys.
- `ifElse` treats a `null` branch as identity of the first argument.
- `propEq` / `pathEq` take the value first (Ramda >= 0.29 order).
- Curried comparisons take the left operand first: `gt(5)` is "5 > …". `ascend`/`descend` use `<=>` (numeric strings
  compare numerically); `ascendWith`/`descendWith` take a comparison function such as `strcmp(...)`.
- `sortBy`, `sortWith`, `uniq`, `chain`, `flatten` return lists; `filter`, `reject`, `partition`, `pluck` preserve
  keys.
- `remap` is our own addition, Ramda has no equivalent. Strings in the spec are always record keys (moved and removed), never callables; arrays are nested specs;
  callables get the whole record and remove nothing. Spec keys win over remaining record keys (the old
  merge/applySpec/omit pattern let record keys win); an `omit()` after `remap()` runs on the result. Built for the
  `converge(merge(), [applySpec(...), omit(...)])` pattern in the consuming projects' SQL read models.

## Conventions

- PHP `^8.4`, `declare(strict_types=1)`, global functions imported with `use function`.
- Every bug fix gets a regression test that fails without the fix.
- `_acceptsArgs()` and `_getArity()` must both check `_curriedArity()` first, otherwise curried callbacks look
  like zero-parameter functions.
- Conventional Commits are required: semantic-release (`.releaserc`, `.github/workflows/release.yaml`) creates tags
  without `v` prefix (`1.0.0`) and GitHub releases from commits on `main`. `fix:` → patch, `feat:` → minor,
  `BREAKING CHANGE:` footer → major. The default (angular) preset ignores a bare `!` in the header, so always add
  the footer. Only mark real breaking changes, 1.x is meant to stay stable.
- Releases run after a green "Build" on main (`workflow_run`). semantic-release is pinned in `release.yaml`; bump it
  deliberately. Merge PRs with a merge commit, a squash keeps only the PR title.
- `main` is protected by a ruleset: changes go through pull requests with green CI checks.

## Outlook

1.x is the curried API for PHP 8.4 and 8.5. PHP 8.6 adds partial function application (`foo(?, 'x')`), which
together with the pipe operator `|>` (PHP 8.5) covers much of what auto-currying does here. A possible 2.0 requiring
PHP 8.6 could switch to typed signatures without auto-currying; that decision is still open.
