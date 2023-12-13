<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\curry1;

/** @covers \Fnc\curry1 */
final class Curry1Test extends TestCase
{
    public function testCurry1(): void
    {
        $sum = curry1(static function ($a) {
            return $a;
        });
        $five = $sum(5);

        $this->assertEquals($sum, $sum());
        $this->assertEquals(5, $five);
        $this->assertEquals(5, $sum(5));
        $this->assertEquals(5, $sum(5, 6, 7));
    }
}
