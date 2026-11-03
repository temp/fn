<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\find;

#[CoversFunction('Fnc\find')]
final class FindTest extends TestCase
{
    public function testFind(): void
    {
        $this->assertSame(['id' => 2], find(static fn ($v) => $v['id'] > 1, [['id' => 1], ['id' => 2], ['id' => 3]]));
    }

    public function testNotFound(): void
    {
        $this->assertNull(find(static fn ($v) => $v > 5, [1, 2]));
    }
}
