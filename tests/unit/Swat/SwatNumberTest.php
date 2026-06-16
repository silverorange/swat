<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SwatI18NLocale::class)]
class SwatNumberTest extends TestCase
{
    #[Test]
    #[DataProviderExternal(RoundToEvenProvider::class, 'halfwayCasesPrecisionZeroProvider')]
    #[DataProviderExternal(RoundToEvenProvider::class, 'halfwayCasesPrecisionOneProvider')]
    #[DataProviderExternal(RoundToEvenProvider::class, 'halfwayCasesPrecisionTwoProvider')]
    #[DataProviderExternal(RoundToEvenProvider::class, 'nonHalfwayCasesProvider')]
    #[DataProviderExternal(RoundToEvenProvider::class, 'integerInputProvider')]
    #[DataProviderExternal(RoundToEvenProvider::class, 'edgeCaseProvider')]
    #[DataProviderExternal(RoundToEvenProvider::class, 'negativePrecisionProvider')]
    #[DataProviderExternal(RoundToEvenProvider::class, 'floatingPointTrapProvider')]
    public function testRoundToEven(
        float|int $value,
        int $precision,
        float $expected
    ): void {
        self::assertSame($expected, SwatNumber::roundToEven($value, $precision));
    }

    #[DataProviderExternal(RoundUpProvider::class, 'halfwayCasesPrecisionZero')]
    #[DataProviderExternal(RoundUpProvider::class, 'halfwayCasesPrecisionOne')]
    #[DataProviderExternal(RoundUpProvider::class, 'halfwayCasesPrecisionTwo')]
    #[DataProviderExternal(RoundUpProvider::class, 'nonHalfwayCases')]
    #[DataProviderExternal(RoundUpProvider::class, 'integerInput')]
    #[DataProviderExternal(RoundUpProvider::class, 'edgeCases')]
    #[DataProviderExternal(RoundUpProvider::class, 'negativePrecision')]
    #[DataProviderExternal(RoundUpProvider::class, 'floatingPointTraps')]
    public function testRoundUp(
        float|int $value,
        int $precision,
        float $expected,
    ): void {
        self::assertSame($expected, SwatNumber::roundUp($value, $precision));
    }

    #[Test]
    #[DataProviderExternal(OrdinalProvider::class, 'ordinalCasesEnUs')]
    public function testOrdinalForEnUs(int $value, string $expected): void
    {
        SwatI18NLocale::setlocale(LC_ALL, 'en_US');
        $this->assertSame($expected, SwatNumber::ordinal($value));
    }

    #[Test]
    #[DataProviderExternal(OrdinalProvider::class, 'ordinalCasesFrFr')]
    public function testOrdinalForFrFr(int $value, string $expected): void
    {
        SwatI18NLocale::setlocale(LC_ALL, 'fr_FR');
        $this->assertSame($expected, SwatNumber::ordinal($value));
    }
}
