<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SwatI18NCurrencyFormat::class)]
class SwatI18NCurrencyFormatTest extends TestCase
{
    protected SwatI18NCurrencyFormat $format;

    public function setUp(): void
    {
        $this->format = new SwatI18NCurrencyFormat();
        $this->format->decimal_separator = '.';
        $this->format->thousands_separator = ',';
        $this->format->grouping = [3];
    }

    public function testOverrideValidProperties()
    {
        $newFormat = $this->format->override([
            'decimal_separator'   => ',',
            'thousands_separator' => '.',
            'grouping'            => [],
            'symbol'              => '$',
            'p_sign'              => '+',
        ]);

        $this->assertNotSame($this->format, $newFormat);
        $this->assertEquals(',', $newFormat->decimal_separator);
        $this->assertEquals('.', $newFormat->thousands_separator);
        $this->assertEquals([], $newFormat->grouping);
        $this->assertEquals('$', $newFormat->symbol);
        $this->assertEquals('+', $newFormat->p_sign);
    }

    public function testOverrideNullValueDoesNotChangeProperty()
    {
        $newFormat = $this->format->override([
            'p_sign_position' => null,
        ]);
        $this->assertEquals(
            1,
            $newFormat->p_sign_position
        );
    }

    public function testOverrideWithInvalidPropertyThrowsException(): void
    {
        $this->expectException(SwatException::class);

        $this->format->override([
            'nonexistent_property' => 'value',
        ]);
    }

    public function testOverrideDoesNotMutateOnInvalidProperty(): void
    {
        try {
            $this->format->override([
                'decimal_separator'    => ',',
                'nonexistent_property' => 'value',
            ]);
            $this->fail('Expected SwatException was not thrown.');
        } catch (SwatException $e) {
            // The original object must be untouched because the validation
            // pass happens before any properties are copied/changed.
            $this->assertSame('.', $this->format->decimal_separator);
        }
    }
}
