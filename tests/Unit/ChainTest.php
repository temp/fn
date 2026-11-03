<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\chain;

#[CoversFunction('Fnc\chain')]
final class ChainTest extends TestCase
{
    public function testChain(): void
    {
        $this->assertSame([1, 1, 2, 2], chain(static fn ($n) => [$n, $n], [1, 2]));
    }

    public function testReturnsList(): void
    {
        $this->assertSame(['a', 'b'], chain(static fn ($v) => ['x' => $v], ['k1' => 'a', 'k2' => 'b']));
    }

    public function testEmptyResults(): void
    {
        $this->assertSame([], chain(static fn () => [], [1, 2]));
    }
}
