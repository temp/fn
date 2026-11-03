<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\complement;
use function Fnc\isEmpty;

#[CoversFunction('Fnc\complement')]
final class ComplementTest extends TestCase
{
    public function testComplement(): void
    {
        $isNotEmpty = complement(isEmpty());

        $this->assertTrue($isNotEmpty('foo'));
    }

    public function testNegatesResult(): void
    {
        $this->assertFalse(complement(static fn () => true)());
        $this->assertTrue(complement(static fn () => false)());
    }

    public function testPassesAllArguments(): void
    {
        $notBothPositive = complement(static fn ($a, $b) => $a > 0 && $b > 0);

        $this->assertFalse($notBothPositive(1, 2));
        $this->assertTrue($notBothPositive(1, -2));
    }
}
