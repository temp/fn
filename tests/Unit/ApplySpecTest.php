<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\always;
use function Fnc\applySpec;
use function Fnc\prop;

/** @covers \Fnc\applySpec */
final class ApplySpecTest extends TestCase
{
    public function testNumericArray(): void
    {
        $applySpec = applySpec([always(1), always(2), always(3)]);

        $result = $applySpec('foo');

        $this->assertSame([1, 2, 3], $result);
    }

    public function testAssociateArray(): void
    {
        $applySpec = applySpec(['a' => always(1), 'b' => always(2), 'c' => always(3)]);

        $result = $applySpec('foo');

        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $result);
    }

    public function testWithProp(): void
    {
        $applySpec = applySpec(['a' => prop('foo'), 'b' => prop('bar'), 'c' => prop('baz')]);

        $result = $applySpec(['foo' => 1, 'bar' => 2, 'baz' => 3]);

        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $result);
    }

    public function testNested(): void
    {
        $applySpec = applySpec([
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
