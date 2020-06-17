<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\reverse;

/**
 * @covers \Fnc\reverse
 */
final class ReverseTest extends TestCase
{
    public function testArrayValue(): void
    {
        $data = ['foo', 'bar', 'baz'];

        $result = reverse($data);

        $this->assertSame(['baz', 'bar', 'foo'], $result);
    }

    public function testCurriedArrayValue(): void
    {
        $data = ['foo', 'bar', 'baz'];

        $reverse = reverse();

        $result = $reverse($data);

        $this->assertSame(['baz', 'bar', 'foo'], $result);
    }
}
