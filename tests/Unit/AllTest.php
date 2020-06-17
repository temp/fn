<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\all;
use function Fnc\isEmpty;

/**
 * @covers \Fnc\all
 */
final class AllTest extends TestCase
{
    public function testAllTrue(): void
    {
        $data = [1, 2];

        $isEmpty = isEmpty();
        $result = all($isEmpty, $data);

        $this->assertFalse($result);
    }

    public function testAllFalse(): void
    {
        $data = [null, null];

        $isEmpty = isEmpty();
        $result = all($isEmpty, $data);

        $this->assertTrue($result);
    }

    public function testCurriedAll(): void
    {
        $data = [1, 2];

        $isEmpty = isEmpty();
        $all = all($isEmpty);

        $result = $all($data);
        $this->assertFalse($result);
    }
}
