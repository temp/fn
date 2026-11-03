<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\assoc;

#[CoversFunction('Fnc\assoc')]
final class AssocTest extends TestCase
{
    public function testAddsKey(): void
    {
        $this->assertSame(['a' => 1, 'b' => 2], assoc('b', 2, ['a' => 1]));
    }

    public function testReplacesKey(): void
    {
        $this->assertSame(['a' => 2], assoc('a', 2, ['a' => 1]));
    }

    public function testDoesNotModifyInput(): void
    {
        $input = ['a' => 1];
        assoc('a', 2, $input);

        $this->assertSame(['a' => 1], $input);
    }

    public function testCurried(): void
    {
        $this->assertSame(['a' => 1, 'b' => 2], assoc('b')(2)(['a' => 1]));
    }
}
