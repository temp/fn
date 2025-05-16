<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\take;

#[CoversFunction('Fnc\take')]
final class TakeTest extends TestCase
{
    public function testNumericArrayValue(): void
    {
        $data = ['foo', 'bar', 'baz'];

        $result = take(2, $data);

        $this->assertSame(['foo', 'bar'], $result);
    }

    public function testCurriedNumericArrayValue(): void
    {
        $data = ['foo', 'bar', 'baz'];

        $take = take(2);

        $result = $take($data);

        $this->assertSame(['foo', 'bar'], $result);
    }

    public function testAssociativeArrayValue(): void
    {
        $data = ['foo' => 1, 'bar' => 2, 'baz' => 3];

        $result = take(2, $data);

        $this->assertSame(['foo' => 1, 'bar' => 2], $result);
    }

    public function testCurriedAssociativeArrayValue(): void
    {
        $data = ['foo' => 1, 'bar' => 2, 'baz' => 3];

        $take = take(2);

        $result = $take($data);

        $this->assertSame(['foo' => 1, 'bar' => 2], $result);
    }

    public function testStringValue(): void
    {
        $data = 'foobar';

        $result = take(3, $data);

        $this->assertSame('foo', $result);
    }

    public function testCurriedStringValue(): void
    {
        $data = 'foobar';

        $take = take(3);

        $result = $take($data);

        $this->assertSame('foo', $result);
    }
}
