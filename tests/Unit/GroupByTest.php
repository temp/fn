<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\groupBy;

#[CoversFunction('Fnc\groupBy')]
final class GroupByTest extends TestCase
{
    public function testGroupBy(): void
    {
        $result = groupBy(static fn ($n) => $n % 2 ? 'odd' : 'even', [1, 2, 3, 4]);

        $this->assertSame(['odd' => [1, 3], 'even' => [2, 4]], $result);
    }

    public function testEmptyList(): void
    {
        $this->assertSame([], groupBy(static fn ($n) => $n, []));
    }
}
