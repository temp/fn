<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\logicXor;

/**
 * @covers \Fnc\logicXor
 */
final class LogicXorTest extends TestCase
{
    public function testLogicXor(): void
    {
        $this->assertFalse(logicXor(false, false));
        $this->assertTrue(logicXor(false, true));
        $this->assertTrue(logicXor(true, false));
        $this->assertFalse(logicXor(true, true));
    }

    public function testCurriedLogicXor(): void
    {
        $xorTrue = logicXor(true);
        $xorFalse = logicXor(false);

        $this->assertTrue($xorTrue(false));
        $this->assertFalse($xorTrue(true));
        $this->assertTrue($xorFalse(true));
        $this->assertFalse($xorFalse(false));
    }
}
