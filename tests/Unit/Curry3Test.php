<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\curry3;

/**
 * @covers \Fnc\curry3
 */
final class Curry3Test extends TestCase
{
    public function testCurry3(): void
    {
        $sum3 = curry3(static function ($a, $b, $c) {
            return $a + $b + $c;
        });
        $plus5 = $sum3(5);
        $plus10 = $plus5(10);
        $minus4 = $plus10(-4);

        $this->assertEquals(11, $minus4);
        $this->assertEquals(11, $plus10(-4));
        $this->assertEquals(11, $plus5(10, -4));
        $this->assertEquals(11, $sum3(5, 10, -4));
    }
}
