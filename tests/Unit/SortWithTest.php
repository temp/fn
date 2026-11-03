<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function array_column;
use function array_map;
use function Fnc\ascend;
use function Fnc\descend;
use function Fnc\prop;
use function Fnc\sortWith;

#[CoversFunction('Fnc\sortWith')]
final class SortWithTest extends TestCase
{
    public function testMultipleComparators(): void
    {
        $records = [
            ['carhouse' => 'b', 'id' => '1'],
            ['carhouse' => 'a', 'id' => '2'],
            ['carhouse' => 'b', 'id' => '0'],
            ['carhouse' => 'a', 'id' => '1'],
        ];

        $sorted = sortWith([ascend(prop('carhouse')), descend(prop('id'))], $records);

        $this->assertSame(
            [['a', '2'], ['a', '1'], ['b', '1'], ['b', '0']],
            array_map(static fn ($r) => [$r['carhouse'], $r['id']], $sorted),
        );
    }

    public function testStableAndReturnsList(): void
    {
        $items = ['x' => ['k' => 1, 'v' => 'a'], 'y' => ['k' => 0, 'v' => 'b'], 'z' => ['k' => 1, 'v' => 'c']];

        $this->assertSame(['b', 'a', 'c'], array_column(sortWith([ascend(prop('k'))], $items), 'v'));
    }

    public function testNoComparatorsKeepsOrder(): void
    {
        $this->assertSame([3, 1, 2], sortWith([], [3, 1, 2]));
    }
}
