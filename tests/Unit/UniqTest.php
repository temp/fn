<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\uniq;

#[CoversFunction('Fnc\uniq')]
final class UniqTest extends TestCase
{
    public function testUniq(): void
    {
        $this->assertSame([1, 2, 3], uniq([1, 2, 1, 3, 2]));
    }

    public function testStrict(): void
    {
        $this->assertSame([1, '1'], uniq([1, '1']));
    }

    public function testUsesEquals(): void
    {
        $this->assertSame([['a' => 1], ['a' => 2]], uniq([['a' => 1], ['a' => 2], ['a' => 1]]));
    }
}
