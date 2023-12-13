<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\isEmpty;
use function Fnc\none;

/** @covers \Fnc\none */
final class NoneTest extends TestCase
{
    public function testAllNotEmpty(): void
    {
        $data = [1, 2];

        $result = none(isEmpty(), $data);

        $this->assertTrue($result);
    }

    public function testAllEmpty(): void
    {
        $data = [null, null];

        $result = none(isEmpty(), $data);

        $this->assertFalse($result);
    }

    public function testPartlyEmpty(): void
    {
        $data = [null, 2];

        $result = none(isEmpty(), $data);

        $this->assertFalse($result);
    }

    public function testCurriedAllNotEmpty(): void
    {
        $data = [1, 2];

        $none = none(isEmpty());
        $result = $none($data);

        $this->assertTrue($result);
    }

    public function testCurriedAllEmpty(): void
    {
        $data = [null, null];

        $none = none(isEmpty());
        $result = $none($data);

        $this->assertFalse($result);
    }

    public function testCurriedPartlyEmpty(): void
    {
        $data = [null, 2];

        $none = none(isEmpty());
        $result = $none($data);

        $this->assertFalse($result);
    }
}
