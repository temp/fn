<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\concat;

/** @covers \Fnc\concat */
final class ConcatTest extends TestCase
{
    public function testNumericArrayValues(): void
    {
        $a = [1, 2, 3];
        $b = [1, 2, 4];
        $c = concat($a);
        $this->assertEquals([1, 2, 3, 1, 2, 4], concat($a, $b));
        $this->assertEquals([1, 2, 3, 1, 2, 4], $c($b));
        $this->assertEquals([1, 2], concat([1, 2], []));
    }

    public function testAssociativeArrayValues(): void
    {
        $a = [1, 2];
        $b = [1, 'b' => 'c'];
        $this->assertEquals([1, 2, 1], concat($a, [1]));
        $this->assertEquals([1, 'b' => 'c', 1], concat($b, [1]));
        $this->assertEquals([1, 'b' => 'c'], concat($b, ['b' => 'c']));
    }
}
