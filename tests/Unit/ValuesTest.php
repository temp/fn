<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;
use stdClass;

use function Fnc\values;

#[CoversFunction('Fnc\values')]
final class ValuesTest extends TestCase
{
    public function testArray(): void
    {
        $this->assertSame([1, 2], values(['a' => 1, 'b' => 2]));
    }

    public function testObject(): void
    {
        $object = new stdClass();
        $object->a = 1;

        $this->assertSame([1], values($object));
    }
}
