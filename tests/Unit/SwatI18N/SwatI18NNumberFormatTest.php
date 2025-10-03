<?php

namespace tests\Unit\SwatI18N;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SwatI18NNumberFormatTest extends TestCase
{
    protected \SwatI18NNumberFormat $format;

    public function setUp(): void
    {
        $this->format = new \SwatI18NNumberFormat();
        $this->format->decimal_separator = '.';
        $this->format->thousands_separator = ',';
        $this->format->grouping = [3];
    }

    public function testOverrideValidProperties()
    {
        $newFormat = $this->format->override([
            'decimal_separator'   => ',',
            'thousands_separator' => '.',
        ]);

        $this->assertNotSame($this->format, $newFormat);
        $this->assertEquals(',', $newFormat->decimal_separator);
        $this->assertEquals('.', $newFormat->thousands_separator);
        $this->assertEquals(
            [3],
            $newFormat->grouping
        );
    }

    public function testOverrideInvalidPropertyThrowsException()
    {
        $this->expectException(\SwatException::class);
        $this->format->override(['invalid_property' => 'value']);
    }

    public function testOverrideNullValueDoesNotChangeProperty()
    {
        $newFormat = $this->format->override([
            'decimal_separator' => null,
        ]);
        $this->assertEquals(
            '.',
            $newFormat->decimal_separator
        );
    }

    public function testToString()
    {
        $expected = "decimal_separator => .\nthousands_separator => ,\ngrouping => 3\n";
        $this->assertEquals(
            $expected,
            (string) $this->format
        );
    }

    public function testToStringWithArrayGrouping()
    {
        $newFormat = $this->format->override([
            'grouping' => 3,
        ]);
        $expected = "decimal_separator => .\nthousands_separator => ,\ngrouping => 3\n";
        $this->assertEquals(
            $expected,
            (string) $newFormat
        );
    }
}
