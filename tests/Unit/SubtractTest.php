<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\subtract;

#[CoversFunction('Fnc\subtract')]
final class SubtractTest extends TestCase
{
    public function testSubtract(): void
    {
        $this->assertSame(7, subtract(10, 3));
        $this->assertSame(-3, subtract(null, 3));
    }

    public function testCurriedFirstArgumentIsMinuend(): void
    {
        $this->assertSame(7, subtract(10)(3));
    }
}
