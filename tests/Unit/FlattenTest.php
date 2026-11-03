<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\flatten;

#[CoversFunction('Fnc\flatten')]
final class FlattenTest extends TestCase
{
    public function testFlatten(): void
    {
        $this->assertSame([1, 2, 3, 4], flatten([1, [2, [3, [4]]]]));
    }

    public function testReturnsList(): void
    {
        $this->assertSame([1, 2], flatten(['a' => 1, 'b' => ['c' => 2]]));
    }
}
