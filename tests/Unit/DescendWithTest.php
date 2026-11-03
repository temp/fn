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
}
