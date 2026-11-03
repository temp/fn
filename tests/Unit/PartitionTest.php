<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\curry2;
use function Fnc\partition;

#[CoversFunction('Fnc\partition')]
final class PartitionTest extends TestCase
{
    public function testPartition(): void
    {
        $result = partition(static fn ($n) => $n % 2 === 0, [1, 2, 3, 4]);

        $this->assertSame([[1 => 2, 3 => 4], [0 => 1, 2 => 3]], $result);
    }

    public function testKeyIsPassedIfAccepted(): void
    {
        $result = partition(static fn ($v, $k) => $k === 'a', ['a' => 1, 'b' => 2]);

        $this->assertSame([['a' => 1], ['b' => 2]], $result);
    }

    public function testInternalFunction(): void
    {
        $this->assertSame([[0 => 1], [1 => 'x']], partition('is_int', [1, 'x']));
    }

    public function testCurriedCallbackReceivesKey(): void
    {
        $isA = curry2(static fn ($v, $k) => $k === 'a');

        $this->assertSame([['a' => 1], ['b' => 2]], partition($isA, ['a' => 1, 'b' => 2]));
    }
}
