<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\max;

#[CoversFunction('Fnc\max')]
final class MaxTest extends TestCase
{
    public function testMax(): void
    {
        $this->assertSame(2, max(1, 2));
        $this->assertSame(2, max(2, 1));
        $this->assertSame('b', max('a', 'b'));
    }
}
