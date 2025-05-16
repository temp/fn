<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function Fnc\curryN;

#[CoversFunction('Fnc\curryN')]
final class CurryNTest extends TestCase
{
    public function testCurryNWithArity2(): void
    {
        $sum2 = curryN(2, static function ($a, $b) {
            return $a + $b;
        });
        $addToFive = $sum2(5);

        $this->assertEquals(8, $addToFive(3));
        $this->assertEquals(8, $sum2(5, 3));
    }

    public function testCurryNWithArity3(): void
    {
        $sum3 = curryN(3, static function ($a, $b, $c) {
            return $a + $b + $c;
        });
        $plus5 = $sum3(5);
        $plus10 = $plus5(10);
        $minus4 = $plus10(-4);

        $this->assertEquals(11, $minus4);
        $this->assertEquals(11, $plus5(10, -4));
        $this->assertEquals(11, $sum3(5, 10, -4));
    }

    public function testCurryNWithArity4(): void
    {
        $sum4 = curryN(4, static function ($a, $b, $c, $d) {
            return $a + $b + $c + $d;
        });
        $plus5 = $sum4(5);
        $plus10 = $plus5(10);
        $minus4 = $plus10(-4);
        $plus4 = $minus4(4);

        $this->assertEquals($sum4, $sum4());
        $this->assertEquals(15, $plus4);
        $this->assertEquals(15, $minus4(4));
        $this->assertEquals(15, $plus10(-4, 4));
        $this->assertEquals(15, $plus5(10, -4, 4));
        $this->assertEquals(15, $sum4(5, 10)(-4, 4));
        $this->assertEquals(15, $sum4(5, 10, -4)(4));
        $this->assertEquals(15, $sum4(5, 10, -4, 4));
        $this->assertEquals(15, $sum4(5, 10, -4, 4, 8));
    }

    public function testCurryNWithArity5(): void
    {
        $sum4 = curryN(5, static function ($a, $b, $c, $d, $e) {
            return $a + $b + $c + $d + $e;
        });
        $plus5 = $sum4(5);
        $plus10 = $plus5(10);
        $minus4 = $plus10(-4);
        $plus4 = $minus4(4);
        $plus2 = $plus4(2);

        $this->assertEquals($sum4, $sum4());
        $this->assertEquals(17, $plus2);
        $this->assertEquals(17, $plus4(2));
        $this->assertEquals(17, $minus4(4, 2));
        $this->assertEquals(17, $plus10(-4, 4, 2));
        $this->assertEquals(17, $plus5(10, -4, 4, 2));
        $this->assertEquals(17, $sum4(5, 10)(-4, 4, 2));
        $this->assertEquals(17, $sum4(5, 10, -4)(4, 2));
        $this->assertEquals(17, $sum4(5, 10, -4, 4, 2));
        $this->assertEquals(17, $sum4(5, 10, -4, 4, 2, 8));
    }

    public function testCurryNWithUnsupportedArityBelow0(): void
    {
        $this->expectException(RuntimeException::class);

        curryN(-1, static function () {
            return null;
        });
    }

    public function testCurryNWithUnsupportedArityAbove5(): void
    {
        $this->expectException(RuntimeException::class);

        curryN(6, static function () {
            return null;
        });
    }
}
