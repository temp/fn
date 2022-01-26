<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\logicAnd;

/**
 * @covers \Fnc\logicAnd
 */
final class LogicAndTest extends TestCase
{
    public function testLogicAnd(): void
    {
        $this->assertFalse(logicAnd(false, false));
        $this->assertFalse(logicAnd(false, true));
        $this->assertFalse(logicAnd(true, false));
        $this->assertTrue(logicAnd(true, true));
    }

    public function testCurriedLogicAnd(): void
    {
        $andTrue = logicAnd(true);
        $andFalse = logicAnd(false);

        $this->assertFalse($andTrue(false));
        $this->assertTrue($andTrue(true));
        $this->assertFalse($andFalse(true));
        $this->assertFalse($andFalse(false));
    }
}
