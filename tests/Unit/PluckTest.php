<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;
use stdClass;

use function Fnc\pluck;

#[CoversFunction('Fnc\pluck')]
final class PluckTest extends TestCase
{
    public function testPluck(): void
    {
        $this->assertSame(['Ada', 'Linus'], pluck('name', [['name' => 'Ada'], ['name' => 'Linus']]));
    }

    public function testObjectsAndMissingKeys(): void
    {
        $object = new stdClass();
        $object->name = 'Ada';

        $this->assertSame(['x' => 'Ada', 'y' => null], pluck('name', ['x' => $object, 'y' => []]));
    }
}
