<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use stdClass;

use function Fnc\prop;

/** @covers \Fnc\prop */
final class PropTest extends TestCase
{
    public function testArrayValue(): void
    {
        $data = ['foo' => 'bar'];

        $result = prop('foo', $data);

        $this->assertSame('bar', $result);
    }

    public function testCurriedArrayValue(): void
    {
        $data = ['foo' => 'bar'];
        $prop = prop('foo');

        $result = $prop($data);

        $this->assertSame('bar', $result);
    }

    public function testObjectValue(): void
    {
        $data = new stdClass();
        $data->foo = 'bar';

        $result = prop('foo', $data);

        $this->assertSame('bar', $result);
    }

    public function testCurriedObjectValue(): void
    {
        $data = new stdClass();
        $data->foo = 'bar';

        $prop = prop('foo');

        $result = $prop($data);

        $this->assertSame('bar', $result);
    }

    public function testArrayKeyNotFound(): void
    {
        $data = ['foo' => 'bar'];

        $result = prop('invalid', $data);

        $this->assertNull($result);
    }

    public function testObjectPropertyNotFound(): void
    {
        $data = new stdClass();
        $data->foo = 'bar';

        $result = prop('invalid', $data);

        $this->assertNull($result);
    }
}
