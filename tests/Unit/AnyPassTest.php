<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\anyPass;

#[CoversFunction('Fnc\anyPass')]
final class AnyPassTest extends TestCase
{
    public function testAnyPass(): void
    {
        $isZeroOrNegative = anyPass([static fn ($n) => $n === 0, static fn ($n) => $n < 0]);

        $this->assertTrue($isZeroOrNegative(0));
        $this->assertTrue($isZeroOrNegative(-1));
        $this->assertFalse($isZeroOrNegative(1));
    }

    public function testEmptyListIsNotSatisfied(): void
    {
        $this->assertFalse(anyPass([])(1));
    }
}
