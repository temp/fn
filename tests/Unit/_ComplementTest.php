<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\_complement;

// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps

/**
 * @covers \Fnc\_complement
 */
final class _ComplementTest extends TestCase
{
    public function testNumericArrayValue(): void
    {
        $true = static fn () => true;
        $false = static fn () => false;

        $trueResult = _complement($true)();
        $falseResult = _complement($false)();

        $this->assertFalse($trueResult);
        $this->assertTrue($falseResult);
    }
}
