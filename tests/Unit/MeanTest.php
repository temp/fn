<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\mean;

#[CoversFunction('Fnc\mean')]
final class MeanTest extends TestCase
{
    public function testMean(): void
    {
        $this->assertSame(2.5, mean([1, 2, 3, 4]));
    }

    public function testEmptyList(): void
    {
        $this->assertNan(mean([]));
    }
}
