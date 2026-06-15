<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SwatI18NLocale::class)]
class SwatI18NLocaleTest extends TestCase
{
    protected SwatI18NLocale $locale;

    public function setUp(): void
    {
        $this->locale = SwatI18NLocale::get();
    }

    #[Test]
    #[DataProviderExternal(SwatI18NRoundingProvider::class, 'halfwayCasesPrecisionZeroProvider')]
    #[DataProviderExternal(SwatI18NRoundingProvider::class, 'halfwayCasesPrecisionOneProvider')]
    #[DataProviderExternal(SwatI18NRoundingProvider::class, 'halfwayCasesPrecisionTwoProvider')]
    #[DataProviderExternal(SwatI18NRoundingProvider::class, 'nonHalfwayCasesProvider')]
    #[DataProviderExternal(SwatI18NRoundingProvider::class, 'integerInputProvider')]
    #[DataProviderExternal(SwatI18NRoundingProvider::class, 'edgeCaseProvider')]
    #[DataProviderExternal(SwatI18NRoundingProvider::class, 'negativePrecisionProvider')]
    #[DataProviderExternal(SwatI18NRoundingProvider::class, 'floatingPointTrapProvider')]
    public function testRoundToEven(float|int $value, int $precision, float $expected): void
    {
        self::assertSame($expected, $this->locale->roundToEven($value, $precision));
    }

}
