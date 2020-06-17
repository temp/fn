<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\not;

/**
 * @covers \Fnc\not
 */
final class NotTest extends TestCase
{
    public function testNot(): void
    {
        $this->assertFalse(not(true));
        $this->assertTrue(not(false));
    }
}
