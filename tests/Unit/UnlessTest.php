<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\isNil;
use function Fnc\unless;

#[CoversFunction('Fnc\unless')]
final class UnlessTest extends TestCase
{
    public function testUnless(): void
    {
        $trimUnlessNull = unless(isNil(), 'trim');

        $this->assertSame('x', $trimUnlessNull(' x '));
        $this->assertNull($trimUnlessNull(null));
    }
}
