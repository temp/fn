<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\converge;

/**
 * @covers \Fnc\converge
 */
final class ConvergeTest extends TestCase
{
    public function testConverge(): void
    {
        $add = static function ($a, $b) {
            return $a + $b;
        };
        $multiply = static function ($a, $b) {
            return $a * $b;
        };
        $subtract = static function ($a, $b) {
            return $a - $b;
        };

        $conv1 = converge($multiply, [$add, $subtract]);
        $this->assertEquals(-3, $conv1(1, 2));

        $add3 = static function ($a, $b, $c) {
            return $a + $b + $c;
        };

        $conv2 = converge($add3, [$multiply, $add, $subtract]);
        $this->assertEquals(4, $conv2(1, 2));

        $a = [$add, $subtract];
        $conv1 = converge($multiply, $a);
        $this->assertEquals(-3, $conv1(1, 2));
    }
}
