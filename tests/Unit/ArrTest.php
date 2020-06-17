<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\always;
use function Fnc\arr;
use function Fnc\prop;
use function set_error_handler;
use const E_USER_DEPRECATED;

/**
 * @covers \Fnc\arr
 */
final class ArrTest extends TestCase
{
    public function testDeprecation(): void
    {
        $wasCalled = false;
        $oldErrorHandler = set_error_handler(function ($errno, $errstr) use (&$wasCalled): void {
            $this->assertSame(E_USER_DEPRECATED, $errno);
            $this->assertSame('arr() is deprecated, use applySpec()', $errstr);
            $wasCalled = true;
        });
        $applySpec = arr([always(1), always(2), always(3)]);
        set_error_handler($oldErrorHandler);

        $applySpec('foo');

        $this->assertTrue($wasCalled);
    }

    public function testNumericArray(): void
    {
        $applySpec = arr([always(1), always(2), always(3)]);

        $result = $applySpec('foo');

        $this->assertSame([1, 2, 3], $result);
    }

    public function testAssociateArray(): void
    {
        $applySpec = arr(['a' => always(1), 'b' => always(2), 'c' => always(3)]);

        $result = $applySpec('foo');

        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $result);
    }

    public function testWithProp(): void
    {
        $applySpec = arr(['a' => prop('foo'), 'b' => prop('bar'), 'c' => prop('baz')]);

        $result = $applySpec(['foo' => 1, 'bar' => 2, 'baz' => 3]);

        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $result);
    }

    public function testNested(): void
    {
        $applySpec = arr([
            'a' => prop('foo'),
            'b' => [
                'c' => prop('bar'),
                'd' => prop('baz'),
            ],
        ]);

        $result = $applySpec(['foo' => 1, 'bar' => 2, 'baz' => 3]);

        $this->assertSame(['a' => 1, 'b' => ['c' => 2, 'd' => 3]], $result);
    }
}
