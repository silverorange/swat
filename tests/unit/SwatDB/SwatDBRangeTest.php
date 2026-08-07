<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SwatDBRange::class)]
class SwatDBRangeTest extends TestCase
{
    #[Test]
    public function testCombiningRanges(): void
    {
        $range1 = new SwatDBRange(10, 100);
        $range2 = new SwatDBRange(20, 160);

        $combined = $range1->combine($range2);

        $this->assertEquals(80, $combined->getLimit());
        $this->assertEquals(100, $combined->getOffset());
    }

    #[Test]
    public function testCombinedRangesWithNoLimit(): void
    {
        $range1 = new SwatDBRange(null, 100);
        $range2 = new SwatDBRange(20, 160);

        $combined = $range1->combine($range2);

        $this->assertEquals(null, $combined->getLimit());
        $this->assertEquals(100, $combined->getOffset());
    }

    #[Test]
    public function testCombinedRangesWithNoOffset(): void
    {
        $range1 = new SwatDBRange(10);
        $range2 = new SwatDBRange(20);

        $combined = $range1->combine($range2);

        $this->assertEquals(20, $combined->getLimit());
        $this->assertEquals(0, $combined->getOffset());
    }

    #[Test]
    public function testCombinedRangesWithNoOffsetOrLimit(): void
    {
        $range1 = new SwatDBRange(10);
        $range2 = new SwatDBRange(null, 20);

        $combined = $range1->combine($range2);

        $this->assertEquals(null, $combined->getLimit());
        $this->assertEquals(0, $combined->getOffset());
    }
}
