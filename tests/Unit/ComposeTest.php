<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\compose;

/**
 * @covers \Fnc\compose
 */
final class ComposeTest extends TestCase
{
    public function testCompose(): void
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
        $combo = compose($triple, $double, $square);

        $result = $combo(5);

        $this->assertEquals(150, $result);
    }
}
