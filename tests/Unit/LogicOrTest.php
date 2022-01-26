<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\logicOr;

/**
 * @covers \Fnc\logicOr
 */
final class LogicOrTest extends TestCase
{
    public function testLogicOr(): void
    {
        $this->assertFalse(logicOr(false, false));
        $this->assertTrue(logicOr(false, true));
        $this->assertTrue(logicOr(true, false));
        $this->assertTrue(logicOr(true, true));
    }

    public function testCurriedLogicOr(): void
    {
        $orTrue = logicOr(true);
        $orFalse = logicOr(false);

        $this->assertTrue($orTrue(false));
        $this->assertTrue($orTrue(true));
        $this->assertTrue($orFalse(true));
        $this->assertFalse($orFalse(false));
    }
}
