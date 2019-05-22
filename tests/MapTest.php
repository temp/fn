<?php

declare(strict_types=1);

namespace FnTests;

use PHPUnit\Framework\TestCase;
use function Fn\map;

/**
 * @covers \Fn\Map
 */
final class MapTest extends TestCase
{
    public function testWithNullSource(): void
    {
        $result = map(function($i) {return $i;}, ['abc' => 'def']);

        $this->assertSame(['abc' => 'def'], $result);
    }
}
