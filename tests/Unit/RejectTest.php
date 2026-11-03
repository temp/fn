<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\reject;

#[CoversFunction('Fnc\reject')]
final class RejectTest extends TestCase
{
    public function testReject(): void
    {
        $this->assertSame([0 => 1, 2 => 3], reject(static fn ($n) => $n % 2 === 0, [1, 2, 3, 4]));
    }

    public function testInternalFunction(): void
    {
        $this->assertSame([1 => 'x'], reject('is_int', [1, 'x']));
    }
}
