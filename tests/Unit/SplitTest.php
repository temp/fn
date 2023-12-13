<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\split;

// phpcs:disable Generic.PHP.ForbiddenFunctions.FoundWithAlternative

final class SplitTest extends TestCase
{
    public function testWithZeroElements(): void
    {
        $data = '';

        $result = split('_', $data);

        $this->assertSame([''], $result);
    }

    public function testCurriedWithZeroElements(): void
    {
        $data = '';

        $split = split('_');

        $result = $split($data);

        $this->assertSame([''], $result);
    }

    public function testWithOneElement(): void
    {
        $data = 'foo';

        $result = split('_', $data);

        $this->assertSame(['foo'], $result);
    }

    public function testCurriedWithOneElement(): void
    {
        $data = 'foo';

        $split = split('_');

        $result = $split($data);

        $this->assertSame(['foo'], $result);
    }

    public function testWithMultipleElements(): void
    {
        $data = 'foo_bar';

        $result = split('_', $data);

        $this->assertSame(['foo', 'bar'], $result);
    }

    public function testCurriedWithMultipleElements(): void
    {
        $data = 'foo_bar';

        $split = split('_');

        $result = $split($data);

        $this->assertSame(['foo', 'bar'], $result);
    }

    public function testWithMultipleMixedElements(): void
    {
        $data = 'foo bar 1 2.5';

        $result = split(' ', $data);

        $this->assertSame(['foo', 'bar', '1', '2.5'], $result);
    }
}
