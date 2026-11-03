<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\min;

#[CoversFunction('Fnc\min')]
final class MinTest extends TestCase
{
    public function testMin(): void
    {
        $this->assertSame(1, min(1, 2));
        $this->assertSame(1, min(2, 1));
        $this->assertSame('a', min('a', 'b'));
    }
}
