<?php

namespace Tests\Unit\Swat;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SwatStringTest extends TestCase
{
    public function testToListSingleItem()
    {
        $result = \SwatString::toList(['foo']);
        $this->assertEquals('foo', $result);
    }

    public function testToListTwoItems()
    {
        $result = \SwatString::toList(['foo', 'bar']);
        $this->assertEquals('foo and bar', $result);
    }

    public function testToListThreeItems()
    {
        $result = \SwatString::toList(['foo', 'bar', 'baz']);
        $this->assertEquals('foo, bar, and baz', $result);
    }

    public function testToListCustomConjunctionAndDelimiter()
    {
        $result = \SwatString::toList(['a', 'b', 'c'], 'or', '; ', false);
        $this->assertEquals('a; b or c', $result);
    }

    public function testToListWithIterator()
    {
        $result = \SwatString::toList(new \ArrayIterator(['x', 'y']));
        $this->assertEquals('x and y', $result);
    }

    public function testToListWithNonIterator()
    {
        $this->expectException(\SwatException::class);
        \SwatString::toList('a string');
    }
}
