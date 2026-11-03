<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function array_column;
use function Fnc\ascend;
use function Fnc\ascendWith;
use function Fnc\prop;
use function Fnc\sortWith;
use function strcmp;

#[CoversFunction('Fnc\ascendWith')]
final class AscendWithTest extends TestCase
{
    public function testByteWiseStringOrder(): void
    {
        $records = [['id' => '9999'], ['id' => '10000'], ['id' => '200']];

        $byteWise = sortWith([ascendWith(strcmp(...), prop('id'))], $records);
        $numeric = sortWith([ascend(prop('id'))], $records);

        $this->assertSame(['10000', '200', '9999'], array_column($byteWise, 'id'));
        $this->assertSame(['200', '9999', '10000'], array_column($numeric, 'id'));
    }

    public function testCurried(): void
    {
        $compare = ascendWith(strcmp(...), prop('id'));

        $this->assertLessThan(0, $compare(['id' => 'a'], ['id' => 'b']));
        $this->assertSame(0, $compare(['id' => 'a'], ['id' => 'a']));
    }
}
