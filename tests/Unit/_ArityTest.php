<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;

use function Fnc\_arity;

/** @covers \Fnc\_arity */
final class _ArityTest extends TestCase
{
    public function testArityWithArity0(): void
    {
        $ar0 = _arity(0, static function () {
            return null;
        });

        $this->assertNull($ar0());
    }

    public function testArityWithArity1(): void
    {
        $ar1 = _arity(1, static fn ($a) => $a);

        $this->assertSame(1, $ar1(1));
    }

    public function testArityWithArity2(): void
    {
        $ar2 = _arity(2, static fn ($a, $b) => $a * $b);

        $this->assertSame(2, $ar2(1, 2));
    }

    public function testArityWithArity3(): void
    {
        $ar3 = _arity(3, static fn ($a, $b, $c) => $a * $b * $c);

        $this->assertSame(6, $ar3(1, 2, 3));
    }

    public function testArityWithArity4(): void
    {
        $ar4 = _arity(4, static fn ($a, $b, $c, $d) => $a * $b * $c * $d);

        $this->assertSame(24, $ar4(1, 2, 3, 4));
    }

    public function testArityWithArity5(): void
    {
        $ar5 = _arity(5, static fn ($a, $b, $c, $d, $e) => $a * $b * $c * $d * $e);

        $this->assertSame(120, $ar5(1, 2, 3, 4, 5));
    }

    public function testArityWithArity6(): void
    {
        $ar6 = _arity(6, static fn ($a, $b, $c, $d, $e, $f) => $a * $b * $c * $d * $e * $f);

        $this->assertSame(720, $ar6(1, 2, 3, 4, 5, 6));
    }

    public function testArityWithArity7(): void
    {
        $ar7 = _arity(7, static fn ($a, $b, $c, $d, $e, $f, $g) => $a * $b * $c * $d * $e * $f * $g);

        $this->assertSame(5040, $ar7(1, 2, 3, 4, 5, 6, 7));
    }

    public function testArityWithArity8(): void
    {
        $ar8 = _arity(8, static fn ($a, $b, $c, $d, $e, $f, $g, $h) => $a * $b * $c * $d * $e * $f * $g * $h);

        $this->assertSame(40320, $ar8(1, 2, 3, 4, 5, 6, 7, 8));
    }

    public function testArityWithArity9(): void
    {
        $ar9 = _arity(9, static fn ($a, $b, $c, $d, $e, $f, $g, $h, $i) => $a * $b * $c * $d * $e * $f * $g * $h * $i);

        $this->assertSame(362880, $ar9(1, 2, 3, 4, 5, 6, 7, 8, 9));
    }

    public function testArityWithArity10(): void
    {
        // phpcs:ignore Generic.Files.LineLength.TooLong
        $ar10 = _arity(10, static fn ($a, $b, $c, $d, $e, $f, $g, $h, $i, $j) => $a * $b * $c * $d * $e * $f * $g * $h * $i * $j);

        $this->assertSame(3628800, $ar10(1, 2, 3, 4, 5, 6, 7, 8, 9, 10));
    }

    public function testArityWithUnsupportedBelow0(): void
    {
        $this->expectException(RuntimeException::class);

        _arity(-1, static fn ($a) => $a);
    }

    public function testArityWithUnsupportedAbove10(): void
    {
        $this->expectException(RuntimeException::class);

        _arity(11, static fn ($a) => $a);
    }
}
