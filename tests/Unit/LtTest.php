<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\lt;

#[CoversFunction('Fnc\lt')]
final class LtTest extends TestCase
{
    public function testCompare(): void
    {
        $this->assertTrue(lt(1, 2));
        $this->assertFalse(lt(1, 1));
        $this->assertFalse(lt(2, 1));
    }

    public function testCurriedFirstArgumentIsLeftOperand(): void
    {
        $this->assertTrue(lt(1)(2));
    }
}
