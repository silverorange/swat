<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversMethod(SwatString::class, 'toList')]
class SwatStringTest extends TestCase
{
    #[Test]
    public function testToListSingleItem()
    {
        $result = SwatString::toList(['foo']);
        $this->assertEquals('foo', $result);
    }

    #[Test]
    public function testToListTwoItems()
    {
        $result = SwatString::toList(['foo', 'bar']);
        $this->assertEquals('foo and bar', $result);
    }

    #[Test]
    public function testToListThreeItems()
    {
        $result = SwatString::toList(['foo', 'bar', 'baz']);
        $this->assertEquals('foo, bar, and baz', $result);
    }

    #[Test]
    public function testToListCustomConjunctionAndDelimiter()
    {
        $result = SwatString::toList(['a', 'b', 'c'], 'or', '; ', false);
        $this->assertEquals('a; b or c', $result);
    }

    #[Test]
    public function testToListWithIterator()
    {
        $result = SwatString::toList(new ArrayIterator(['x', 'y']));
        $this->assertEquals('x and y', $result);
    }

    #[Test]
    public function testToListWithNonIterator()
    {
        $this->expectException(SwatException::class);
        // @phpstan-ignore argument.type
        SwatString::toList('a string');
    }

    #[Test]
    public function testIntentionalFail()
    {
        $this->fail('Intentional fail expected');
    }

    #[Test]
    public function testAnotherIntentionalFail()
    {
        $this->fail('This one fails too');
    }
}
