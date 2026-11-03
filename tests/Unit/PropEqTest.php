<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\propEq;

#[CoversFunction('Fnc\propEq')]
final class PropEqTest extends TestCase
{
    public function testPropEq(): void
    {
        $this->assertTrue(propEq('active', 'status', ['status' => 'active']));
        $this->assertFalse(propEq('active', 'status', ['status' => 'inactive']));
        $this->assertFalse(propEq('1', 'id', ['id' => 1]));
    }

    public function testCurried(): void
    {
        $isActive = propEq('active', 'status');

        $this->assertTrue($isActive(['status' => 'active']));
    }
}
