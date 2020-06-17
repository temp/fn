<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\unapply;

/**
 * @covers \Fnc\unapply
 */
final class UnapplyTest extends TestCase
{
    public function testUnapply(): void
    {
        $unapply = unapply('json_encode');

        $result = $unapply(1, '3', 1 + 1, 2, ['a' => 'b']);

        $this->assertEquals('[1,"3",2,2,{"a":"b"}]', $result);
    }
}
