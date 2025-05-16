<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\curry4;

#[CoversFunction('Fnc\curry4')]
final class Curry4Test extends TestCase
{
    public function testCurry3(): void
    {
        $sum4 = curry4(static function ($a, $b, $c, $d) {
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
}
