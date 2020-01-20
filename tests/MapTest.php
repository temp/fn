<?php

declare(strict_types=1);

namespace FncTests;

use PHPUnit\Framework\TestCase;
use function Fnc\map;

/**
 * @covers \Fnc\Map
 */
final class MapTest extends TestCase
{
    public function testWithNullSource(): void
    {
        $result = map(function($i) {return $i;}, ['abc' => 'def']);

        $this->assertSame(['abc' => 'def'], $result);
    }
}
