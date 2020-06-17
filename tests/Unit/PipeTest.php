<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\pipe;

/**
 * @covers \Fnc\pipe
 */
final class PipeTest extends TestCase
{
    public function testNumericArrayValue(): void
    {
        $triple = static function ($x) {
            return $x * 3;
        };
        $double = static function ($x) {
            return $x * 2;
        };
        $square = static function ($x) {
            return $x * $x;
        };

        $pipe = pipe($square, $double, $triple);

        $this->assertEquals(150, $pipe(5));
    }
}
