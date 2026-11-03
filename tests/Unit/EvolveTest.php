<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\evolve;
use function Fnc\inc;

#[CoversFunction('Fnc\evolve')]
final class EvolveTest extends TestCase
{
    public function testEvolve(): void
    {
        $result = evolve(['count' => inc(), 'name' => 'strtoupper'], ['count' => 1, 'name' => 'a', 'other' => 'b']);

        $this->assertSame(['count' => 2, 'name' => 'A', 'other' => 'b'], $result);
    }

    public function testNested(): void
    {
        $result = evolve(['user' => ['age' => inc()]], ['user' => ['age' => 1, 'name' => 'Ada']]);

        $this->assertSame(['user' => ['age' => 2, 'name' => 'Ada']], $result);
    }

    public function testMissingKeysAreNotAdded(): void
    {
        $this->assertSame(['a' => 1], evolve(['b' => inc()], ['a' => 1]));
    }

    public function testCurried(): void
    {
        $this->assertSame(['a' => 2], evolve(['a' => inc()])(['a' => 1]));
    }
}
