<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SwatI18NNumberFormat::class)]
class SwatI18NNumberFormatTest extends TestCase
{
    protected SwatI18NNumberFormat $format;

    protected function setUp(): void
    {
        $this->format = new SwatI18NNumberFormat();
        $this->format->decimal_separator = '.';
        $this->format->thousands_separator = ',';
        $this->format->grouping = [3];
    }

    #[Test]
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

    #[Test]
    public function testOverrideInvalidPropertyThrowsException()
    {
        $this->expectException(SwatException::class);
        $this->format->override(['invalid_property' => 'value']);
    }

    #[Test]
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

    #[Test]
    public function testToString()
    {
        $expected = "decimal_separator => .\nthousands_separator => ,\ngrouping => 3\n";
        $this->assertEquals(
            $expected,
            (string) $this->format
        );
    }

    #[Test]
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
