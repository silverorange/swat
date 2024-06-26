<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class SwatDateTest extends TestCase
{

    #[Test]
    public function itCanBeSerialized(): void
    {
        $date = new SwatDate('2024-07-25 12:34:56', new DateTimeZone('UTC'));
        $serialized = serialize($date);

        $expected = 'O:8:"SwatDate":3:{s:4:"date";s:26:"2024-07-25 12:34:56.000000";' .
            's:13:"timezone_type";i:3;s:8:"timezone";s:3:"UTC";}';

        $this->assertSame($expected, $serialized);
    }

    #[Test]
    public function itCanBeUnserialized(): void
    {
        $date = new SwatDate('2024-07-25 12:34:56', new DateTimeZone('UTC'));
        $serialized = serialize($date);
        $unserialized = unserialize($serialized);

        $this->assertObjectEquals($date, $unserialized);
    }
}
