<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;
use stdClass;

use function Fnc\has;

#[CoversFunction('Fnc\has')]
final class HasTest extends TestCase
{
    public function testArray(): void
    {
        $this->assertTrue(has('a', ['a' => null]));
        $this->assertFalse(has('b', ['a' => 1]));
    }

    public function testObject(): void
    {
        $object = new stdClass();
        $object->a = null;

        $this->assertTrue(has('a', $object));
        $this->assertFalse(has('b', $object));
    }

    public function testNonPublicProperty(): void
    {
        $object = new class {
            // @phpstan-ignore property.onlyWritten
            private string $hidden = 'x'; // phpcs:ignore
        };

        $this->assertFalse(has('hidden', $object));
    }

    public function testScalar(): void
    {
        $this->assertFalse(has('a', 'string'));
    }
}
