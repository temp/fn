<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\defaultTo;

use const NAN;

#[CoversFunction('Fnc\defaultTo')]
final class DefaultToTest extends TestCase
{
    public function testDefaultForNullAndNan(): void
    {
        $this->assertSame(42, defaultTo(42, null));
        $this->assertSame(42, defaultTo(42, NAN));
    }

    public function testKeepsOtherValues(): void
    {
        $this->assertSame(0, defaultTo(42, 0));
        $this->assertSame('', defaultTo(42, ''));
        $this->assertFalse(defaultTo(42, false));
    }

    public function testCurried(): void
    {
        $this->assertSame(42, defaultTo(42)(null));
    }
}
