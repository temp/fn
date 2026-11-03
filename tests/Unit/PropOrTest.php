<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\propOr;

#[CoversFunction('Fnc\propOr')]
final class PropOrTest extends TestCase
{
    public function testPropOr(): void
    {
        $this->assertSame(1, propOr('default', 'a', ['a' => 1]));
        $this->assertSame('default', propOr('default', 'x', ['a' => 1]));
        $this->assertSame('default', propOr('default', 'a', ['a' => null]));
        $this->assertSame(0, propOr('default', 'a', ['a' => 0]));
    }
}
