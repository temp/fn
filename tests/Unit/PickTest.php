<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\pick;

#[CoversFunction('Fnc\pick')]
final class PickTest extends TestCase
{
    public function testPick(): void
    {
        $this->assertSame(['c' => 3, 'a' => 1], pick(['c', 'a', 'x'], ['a' => 1, 'b' => 2, 'c' => 3]));
    }

    public function testNullValuesArePicked(): void
    {
        $this->assertSame(['a' => null], pick(['a'], ['a' => null]));
    }
}
