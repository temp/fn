<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\isEmpty;
use function Fnc\pathSatisfies;

#[CoversFunction('Fnc\pathSatisfies')]
final class PathSatisfiesTest extends TestCase
{
    public function testPathSatisfies(): void
    {
        $data = ['address' => ['zip' => '', 'city' => 'Berlin']];

        $this->assertTrue(pathSatisfies(isEmpty(), ['address', 'zip'], $data));
        $this->assertFalse(pathSatisfies(isEmpty(), ['address', 'city'], $data));
    }
}
