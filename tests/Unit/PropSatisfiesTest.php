<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\isEmpty;
use function Fnc\propSatisfies;

#[CoversFunction('Fnc\propSatisfies')]
final class PropSatisfiesTest extends TestCase
{
    public function testPropSatisfies(): void
    {
        $this->assertTrue(propSatisfies(isEmpty(), 'id', ['id' => '']));
        $this->assertFalse(propSatisfies(isEmpty(), 'id', ['id' => 'x']));
        $this->assertTrue(propSatisfies(isEmpty(), 'missing', ['id' => 'x']));
    }

    public function testCurried(): void
    {
        $isCompleted = propSatisfies(static fn ($v) => (int) $v === 1, 'completed');

        $this->assertTrue($isCompleted(['completed' => '1']));
        $this->assertFalse($isCompleted(['completed' => '0']));
    }
}
