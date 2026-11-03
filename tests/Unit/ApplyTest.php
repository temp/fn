<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\apply;
use function max;

#[CoversFunction('Fnc\apply')]
final class ApplyTest extends TestCase
{
    public function testArrayValue(): void
    {
        $data = [1, 99, 30];
        $max = static fn (int $first, int ...$rest) => max($first, ...$rest);

        $result = apply($max, $data);

        $this->assertSame(99, $result);
    }

    public function testCurriedArrayValue(): void
    {
        $data = [1, 99, 30];
        $max = static fn (int $first, int ...$rest) => max($first, ...$rest);

        $apply = apply($max);

        $result = $apply($data);

        $this->assertSame(99, $result);
    }
}
