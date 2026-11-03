<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\dec;

#[CoversFunction('Fnc\dec')]
final class DecTest extends TestCase
{
    public function testDec(): void
    {
        $this->assertSame(1, dec(2));
        $this->assertSame(0.5, dec(1.5));
    }
}
