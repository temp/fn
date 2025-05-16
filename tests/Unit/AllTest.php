<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\all;
use function Fnc\isEmpty;

#[CoversFunction('Fnc\all')]
final class AllTest extends TestCase
{
    public function testAllNotEmpty(): void
    {
        $data = [1, 2];

        $result = all(isEmpty(), $data);

        $this->assertFalse($result);
    }

    public function testAllEmpty(): void
    {
        $data = [null, null];

        $result = all(isEmpty(), $data);

        $this->assertTrue($result);
    }

    public function testPartlyEmpty(): void
    {
        $data = [null, 2];

        $result = all(isEmpty(), $data);

        $this->assertFalse($result);
    }

    public function testCurriedAllNotEmpty(): void
    {
        $data = [1, 2];

        $all = all(isEmpty());

        $result = $all($data);
        $this->assertFalse($result);
    }

    public function testCurriedAllEmpty(): void
    {
        $data = [null, null];

        $all = all(isEmpty());

        $result = $all($data);
        $this->assertTrue($result);
    }

    public function testCurriedPartlyEmpty(): void
    {
        $data = [null, 2];

        $result = all(isEmpty(), $data);

        $this->assertFalse($result);
    }
}
