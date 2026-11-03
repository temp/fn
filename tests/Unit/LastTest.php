<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\last;

#[CoversFunction('Fnc\last')]
final class LastTest extends TestCase
{
    public function testList(): void
    {
        $this->assertSame(3, last([1, 2, 3]));
        $this->assertSame(2, last(['a' => 1, 'b' => 2]));
    }

    public function testEmptyList(): void
    {
        $this->assertNull(last([]));
    }

    public function testString(): void
    {
        $this->assertSame('c', last('abc'));
        $this->assertSame('', last(''));
    }
}
