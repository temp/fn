<?php

declare(strict_types=1);

namespace FncTests\Exception;

use Fnc\Exception\ElementNotFound;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Fnc\Exception\ElementNotFound
 */
final class ElementNotFoundTest extends TestCase
{
    public function testCreateFromElement(): void
    {
        $e = ElementNotFound::createFromElement('test');

        $this->assertSame('Element test not found.', $e->getMessage());
    }
}
