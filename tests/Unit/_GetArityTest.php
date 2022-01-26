<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

use function Fnc\_getArity;

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
// phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:disable Squiz.Functions.GlobalFunction.Found

/**
 * @covers \Fnc\_getArity
 */
final class _GetArityTest extends TestCase
{
    public static function foo(string $x, int $y): void
    {
    }

    public function testStaticMethodString(): void
    {
        $result = _getArity('\FncTests\Unit\_GetArityTest::foo');

        $this->assertEquals(2, $result);
    }

    public function testStaticMethodArray(): void
    {
        $result = _getArity(['\FncTests\Unit\_GetArityTest', 'foo']);

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
