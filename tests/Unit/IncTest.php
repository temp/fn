<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\inc;

#[CoversFunction('Fnc\inc')]
final class IncTest extends TestCase
{
    public function testInc(): void
    {
        $this->assertSame(3, inc(2));
        $this->assertSame(2.5, inc(1.5));
    }
}
