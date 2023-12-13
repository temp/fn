<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\identity;

/** @covers \Fnc\identity */
final class IdentityTest extends TestCase
{
    public function testIdentity(): void
    {
        $data = [1, 2, 3];

        $result = identity($data);

        $this->assertSame($data, $result);
    }

    public function testCurriedInt(): void
    {
        $data = [1, 2, 3];

        $identity = identity();

        $result = $identity($data);

        $this->assertSame($data, $result);
    }
}
