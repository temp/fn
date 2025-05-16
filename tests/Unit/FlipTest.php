<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\flip;

#[CoversFunction('Fnc\flip')]
final class FlipTest extends TestCase
{
    public function testFlip(): void
    {
        $f = static function ($a, $b) {
            return $a . $b;
        };
        $g = flip($f);
        $this->assertEquals('ba', $g('a', 'b'));

        $f = static function ($a, $b, $c) {
            return $a . $b . $c;
        };
        $g = flip($f);
        $this->assertEquals('bac', $g('a', 'b', 'c'));
    }
}
