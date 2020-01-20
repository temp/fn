<?php

declare(strict_types=1);

namespace FncTests;

use Fnc\Exception\ElementNotFound;
use PHPUnit\Framework\TestCase;
use function Fnc\prop;

/**
 * @covers \Fnc\Prop
 */
final class PropTest extends TestCase
{
    public function testSimplePick(): void
    {
        $prop = prop('foo');

        $result = $prop(['foo' => 'bar']);

        $this->assertSame('bar', $result);
    }

    public function testElementNotFound(): void
    {
        $this->expectException(ElementNotFound::class);

        $prop = prop('invalid');

        $prop(['foo' => 'bar']);
    }
}
