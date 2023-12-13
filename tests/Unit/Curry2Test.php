<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\curry2;

/** @covers \Fnc\curry2 */
final class Curry2Test extends TestCase
{
    public function testCurry2(): void
    {
        $sum2 = curry2(static function ($a, $b) {
            return $a + $b;
        });
        $addToFive = $sum2(5);
        $addToThree = $addToFive(3);

        $this->assertEquals($sum2, $sum2());
        $this->assertEquals(8, $addToThree);
        $this->assertEquals(8, $addToFive(3));
        $this->assertEquals(8, $sum2(3, 5));
        $this->assertEquals(8, $sum2(3, 5, 7));
    }
}
