<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

use function Fnc\_getArity;
use function Fnc\curry2;
use function Fnc\curry3;

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
// phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Functions.GlobalFunction.Found

#[CoversFunction('Fnc\_getArity')]
final class InternalGetArityTest extends TestCase
{
    public static function foo(string $x, int $y): void
    {
    }

    public function testStaticMethodString(): void
    {
        $result = _getArity('\FncTests\Unit\InternalGetArityTest::foo');

        $this->assertEquals(2, $result);
    }

    public function testStaticMethodArray(): void
    {
        $result = _getArity(['\FncTests\Unit\InternalGetArityTest', 'foo']);

        $this->assertEquals(2, $result);
    }

    public function testInstanceMethodArray(): void
    {
        $result = _getArity([$this, 'foo']);

        $this->assertEquals(2, $result);
    }

    public function testClosure(): void
    {
        $foo = static function (float $x, int $y, string $z): void {
        };

        $result = _getArity($foo);

        $this->assertEquals(3, $result);
    }

    public function testFunction(): void
    {
        $result = _getArity('\FncTests\Unit\foo');

        $this->assertEquals(3, $result);
    }

    public function testInvokableClass(): void
    {
        $bar = new Bar();

        $result = _getArity($bar);

        $this->assertEquals(2, $result);
    }

    public function testGetInvalidFunction(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not examine callback');

        _getArity('foo');
    }

    public function testInvalidObject(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not examine callback');

        _getArity(new stdClass());
    }

    public function testCurriedClosures(): void
    {
        $fn3 = static fn ($a, $b, $c) => $a + $b + $c;

        $this->assertSame(2, _getArity(curry2(static fn ($a, $b) => $a + $b)));
        $this->assertSame(3, _getArity(curry3($fn3)));
        $this->assertSame(2, _getArity(curry3($fn3)(1)));
        $this->assertSame(1, _getArity(curry3($fn3)(1, 2)));
    }
}

function foo(int $x, float $y, string $z): void
{
}

class Bar
{
    public function __invoke(float $a, int $b): void
    {
    }
}
