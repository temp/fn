<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;
use stdClass;

use function Fnc\keys;

#[CoversFunction('Fnc\keys')]
final class KeysTest extends TestCase
{
    public function testArray(): void
    {
        $this->assertSame(['a', 'b'], keys(['a' => 1, 'b' => 2]));
    }

    public function testObject(): void
    {
        $object = new stdClass();
        $object->a = 1;

        $this->assertSame(['a'], keys($object));
    }
}
