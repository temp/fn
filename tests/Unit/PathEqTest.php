<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\pathEq;

#[CoversFunction('Fnc\pathEq')]
final class PathEqTest extends TestCase
{
    public function testPathEq(): void
    {
        $user = ['address' => ['city' => 'Berlin']];

        $this->assertTrue(pathEq('Berlin', ['address', 'city'], $user));
        $this->assertFalse(pathEq('Hamburg', ['address', 'city'], $user));
        $this->assertFalse(pathEq('Berlin', ['address', 'zip'], $user));
    }

    public function testCurried(): void
    {
        $this->assertTrue(pathEq(1)(['a'])(['a' => 1]));
    }
}
