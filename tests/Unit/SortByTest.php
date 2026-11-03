<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function array_column;
use function Fnc\prop;
use function Fnc\sortBy;

#[CoversFunction('Fnc\sortBy')]
final class SortByTest extends TestCase
{
    public function testSortBy(): void
    {
        $users = [['name' => 'b', 'age' => 30], ['name' => 'a', 'age' => 20], ['name' => 'c', 'age' => 25]];

        $this->assertSame(['a', 'c', 'b'], array_column(sortBy(prop('age'), $users), 'name'));
    }

    public function testStable(): void
    {
        $items = [['k' => 1, 'v' => 'a'], ['k' => 0, 'v' => 'b'], ['k' => 1, 'v' => 'c']];

        $this->assertSame(['b', 'a', 'c'], array_column(sortBy(prop('k'), $items), 'v'));
    }

    public function testReturnsList(): void
    {
        $this->assertSame([1, 2], sortBy(static fn ($n) => $n, ['x' => 2, 'y' => 1]));
    }
}
