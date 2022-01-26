<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\curry5;

/**
 * @covers \Fnc\curry5
 */
final class Curry5Test extends TestCase
{
    public function testCurry3(): void
    {
        $sum4 = curry5(static function ($a, $b, $c, $d, $e) {
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
}
