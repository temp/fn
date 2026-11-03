<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\pathOr;

#[CoversFunction('Fnc\pathOr')]
final class PathOrTest extends TestCase
{
    public function testPathOr(): void
    {
        $data = ['a' => ['b' => 1, 'n' => null]];

        $this->assertSame(1, pathOr('default', ['a', 'b'], $data));
        $this->assertSame('default', pathOr('default', ['a', 'x'], $data));
        $this->assertSame('default', pathOr('default', ['a', 'n'], $data));
    }
}
