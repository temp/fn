<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function array_column;
use function Fnc\ascendWith;
use function Fnc\descendWith;
use function Fnc\prop;
use function Fnc\sortWith;
use function strcmp;

#[CoversFunction('Fnc\descendWith')]
final class DescendWithTest extends TestCase
{
    public function testDescendWith(): void
    {
        $records = [['id' => '9999'], ['id' => '10000'], ['id' => '200']];

        $sorted = sortWith([descendWith(strcmp(...), prop('id'))], $records);

        $this->assertSame(['9999', '200', '10000'], array_column($sorted, 'id'));
    }

    public function testTieBreaker(): void
    {
        $records = [['g' => 'a', 'id' => '1'], ['g' => 'b', 'id' => '2'], ['g' => 'a', 'id' => '3']];

        $sorted = sortWith([ascendWith(strcmp(...), prop('g')), descendWith(strcmp(...), prop('id'))], $records);

        $this->assertSame(['3', '1', '2'], array_column($sorted, 'id'));
    }

    public function testFloatComparator(): void
    {
        $values = [['v' => 0.1], ['v' => 0.3], ['v' => 0.2]];

        $sorted = sortWith([descendWith(static fn ($a, $b) => $a - $b, prop('v'))], $values);

        $this->assertSame([0.3, 0.2, 0.1], array_column($sorted, 'v'));
    }
}
