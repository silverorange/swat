<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests for SwatI18NLocale.
 *
 * These tests depend on the en_US.UTF-8 locale being installed on the
 * system running the tests. If it is not available, the affected tests are
 * skipped rather than failed.
 *
 * @internal
 */
#[CoversClass(SwatI18NLocale::class)]
class SwatI18NLocaleTest extends TestCase
{
    /**
     * Candidate identifiers for the US English locale across platforms.
     *
     * @var list<string>
     */
    private const EN_US = [
        'en_US.UTF-8',
        'en_US.utf8',
        'en_US',
        'English_United States.1252',
    ];

    /**
     * Candidate identifiers for a comma-decimal locale across platforms.
     *
     * @var list<string>
     */
    private const DE_DE = [
        'de_DE.UTF-8',
        'de_DE.utf8',
        'de_DE',
        'German_Germany.1252',
    ];

    private ?SwatI18NLocale $locale = null;

    private string $original_locale;

    /**
     * Skips the current test unless SwatI18NLocale::get() actually rejects an
     * invalid locale on this platform.
     *
     * glibc returns false for unknown locales, so get() throws. But
     * macOS/BSD setlocale() is permissive and may "accept" nonsense
     * identifiers, in which case the invalid-locale code paths simply cannot
     * be exercised. Rather than guess at setlocale()'s behavior (which has
     * diverged from get()'s real behavior in practice — codeset handling,
     * the LC_ALL wrapper, and array handling all differ), we probe the exact
     * code path the tests rely on and skip if it does not throw.
     */
    private function requireInvalidLocaleRejection(): void
    {
        $probe = 'zz_INVALID_LOCALE.bogus';

        try {
            SwatI18NLocale::get($probe);
            $rejected = false;
        } catch (SwatException $e) {
            $rejected = true;
        } finally {
            // The probe may have cached a valid object under the probe key;
            // wipe it so it cannot affect the test that follows.
            SwatI18NLocale::clearLocaleCache();
        }

        if (!$rejected) {
            $this->markTestSkipped(
                'This platform does not reject invalid locales, so '
                . 'invalid-locale handling cannot be tested here.',
            );
        }
    }

    protected function setUp(): void
    {
        // Clears the private static locale cache so tests cannot leak state into
        // one another (it is keyed per-process and otherwise persists).
        SwatI18NLocale::clearLocaleCache();

        // Remember the system locale so each test runs in isolation.
        $this->original_locale = setlocale(LC_ALL, '0');

        try {
            $this->locale = SwatI18NLocale::get(self::EN_US);
        } catch (SwatException $e) {
            $this->locale = null;
        }

        setlocale(LC_ALL, $this->original_locale);

        if ($this->locale === null) {
            $this->markTestSkipped(
                'No en_US UTF-8 locale is available on this system.',
            );
        }
    }

    protected function tearDown(): void
    {
        // Restore the original system locale.
        setlocale(LC_ALL, $this->original_locale);
    }

    #[Test]
    public function testGetReturnsLocaleObject(): void
    {
        $this->assertInstanceOf(SwatI18NLocale::class, $this->locale);
    }

    #[Test]
    public function testGetReturnsCachedInstance(): void
    {
        $locale1 = SwatI18NLocale::get(self::EN_US);
        $locale2 = SwatI18NLocale::get(self::EN_US);

        // The same locale identifier should yield the cached instance.
        $this->assertSame($locale1, $locale2);
    }

    #[Test]
    public function testGetWithInvalidLocaleThrowsException(): void
    {
        $this->requireInvalidLocaleRejection();

        $this->expectException(SwatException::class);
        SwatI18NLocale::get('this_is_not_a_real_locale.bogus');
    }

    #[Test]
    public function testGetWithArrayDoesNotCacheSkippedIdentifiers(): void
    {
        $this->requireInvalidLocaleRejection();

        // Resolve a valid object via an array whose first entry is invalid.
        SwatI18NLocale::get([
            'skipped_invalid.bogus',
            ...self::EN_US,
        ]);

        // The skipped identifier must NOT have been cached as a valid locale;
        // requesting it on its own must still throw.
        $this->expectException(SwatException::class);
        SwatI18NLocale::get('skipped_invalid.bogus');
    }

    #[Test]
    public function testGetWithArrayOfAllInvalidThrowsException(): void
    {
        $this->requireInvalidLocaleRejection();

        $this->expectException(SwatException::class);
        SwatI18NLocale::get(['not_a_real_locale.bogus', 'also_not_real.bogus']);
    }

    #[Test]
    public function testGetWithNullUsesCurrentLocale(): void
    {
        $locale = SwatI18NLocale::get(null);

        $this->assertInstanceOf(SwatI18NLocale::class, $locale);
    }

    #[Test]
    public function testGetLocaleInfoReturnsArray(): void
    {
        $info = $this->locale->getLocaleInfo();

        $this->assertIsArray($info);

        // Spot-check a few of the keys
        $this->assertArrayHasKey('decimal_point', $info);
        $this->assertArrayHasKey('thousands_sep', $info);
        $this->assertArrayHasKey('currency_symbol', $info);
        $this->assertArrayHasKey('frac_digits', $info);
    }

    #[Test]
    public function testGetLocaleInfoForEnUs(): void
    {
        $info = $this->locale->getLocaleInfo();

        $this->assertSame('.', $info['decimal_point']);
        $this->assertSame(',', $info['thousands_sep']);
    }

    #[Test]
    public function testGetNumberFormat(): void
    {
        $format = $this->locale->getNumberFormat();

        $this->assertInstanceOf(SwatI18NNumberFormat::class, $format);
        $this->assertSame('.', $format->decimal_separator);
        $this->assertSame(',', $format->thousands_separator);
    }

    #[Test]
    public function testGetNumberFormatReturnsCopy(): void
    {
        $format1 = $this->locale->getNumberFormat();
        $format2 = $this->locale->getNumberFormat();

        // Each call must return a fresh clone, so mutating one does not
        // affect the locale's internal state or other copies.
        $this->assertNotSame($format1, $format2);

        $format1->decimal_separator = 'X';
        $this->assertSame('.', $this->locale->getNumberFormat()->decimal_separator);
    }

    #[Test]
    public function testGetNationalCurrencyFormat(): void
    {
        $format = $this->locale->getNationalCurrencyFormat();

        $this->assertInstanceOf(SwatI18NCurrencyFormat::class, $format);
    }

    #[Test]
    public function testGetInternationalCurrencyFormat(): void
    {
        $format = $this->locale->getInternationalCurrencyFormat();

        $this->assertInstanceOf(SwatI18NCurrencyFormat::class, $format);
    }

    #[Test]
    public function testGetInternationalCurrencySymbol(): void
    {
        $symbol = $this->locale->getInternationalCurrencySymbol();

        // Should be the 3-char ISO code with the C99 spacing char stripped.
        $this->assertSame('USD', $symbol);
    }

    #[Test]
    public function testFormatNumberWhole(): void
    {
        $this->assertSame(
            '1,000',
            $this->locale->formatNumber(1000)
        );
    }

    #[Test]
    public function testFormatNumberWithDecimals(): void
    {
        $this->assertSame(
            '1,234.56',
            $this->locale->formatNumber(1234.56, 2),
        );
    }

    #[Test]
    public function testFormatNumberNegative(): void
    {
        $this->assertSame(
            '-1,234.56',
            $this->locale->formatNumber(-1234.56, 2),
        );
    }

    #[Test]
    public function testFormatNumberZero(): void
    {
        $this->assertSame(
            '0',
            $this->locale->formatNumber(0)
        );
    }

    #[Test]
    public function testFormatNumberZeroWithDecimals(): void
    {
        $this->assertSame(
            '0.00',
            $this->locale->formatNumber(0, 2)
        );
    }

    #[Test]
    public function testFormatNumberLargeValueGroupings(): void
    {
        $this->assertSame(
            '1,234,567',
            $this->locale->formatNumber(1234567),
        );
    }

    #[Test]
    public function testFormatNumberRoundsToDecimals(): void
    {
        $this->assertSame(
            '1.24',
            $this->locale->formatNumber(1.235, 2),
        );
    }

    #[Test]
    public function testFormatNumberPadsDecimals(): void
    {
        $this->assertSame(
            '5.50',
            $this->locale->formatNumber(5.5, 2),
        );
    }

    #[Test]
    public function testFormatNumberWithOverriddenGrouping(): void
    {
        // Disabling groupings should remove the thousands separators.
        $this->assertSame(
            '1000000',
            $this->locale->formatNumber(
                1000000,
                null,
                ['thousands_separator' => ''],
            ),
        );
    }

    #[Test]
    public function testFormatNumberWithOverriddenSeparators(): void
    {
        // Mimic a European-style format.
        $this->assertSame(
            '1.234,56',
            $this->locale->formatNumber(
                1234.56,
                2,
                [
                    'decimal_separator'   => ',',
                    'thousands_separator' => '.',
                ],
            ),
        );
    }

    #[Test]
    public function testFormatNumberWithInvalidOverrideThrowsException(): void
    {
        $this->expectException(SwatException::class);

        $this->locale->formatNumber(
            1,
            null,
            ['bogus_property' => 'x']
        );
    }

    #[Test]
    public function testFormatNumberAutoDetectsPrecision(): void
    {
        // With $decimals = null, the fractional precision is auto-detected.
        $this->assertSame(
            '1.5',
            $this->locale->formatNumber(1.5),
        );
    }

    #[Test]
    public function testFormatNumberAutoDetectsPrecisionUnderForeignSystemLocale(): void
    {
        // Force the *process-global* locale to one whose decimal separator is a
        // comma. This is the exact condition that broke the previous test: the
        // detector used to read the ambient locale's decimal_point and search
        // for it in (string)$value, which PHP always renders with '.'.
        $applied = setlocale(LC_ALL, self::DE_DE);
        if ($applied === false) {
            $this->markTestSkipped(
                'No comma-decimal locale is available on this system.',
            );
        }

        try {
            // Sanity check: confirm the ambient locale really uses a comma, so a
            // pass here is meaningful and not a no-op on a dot-decimal box.
            $this->assertSame(',', localeconv()['decimal_point']);

            // The en_US locale object must still auto-detect 1 fractional digit,
            // independent of the comma-decimal system locale.
            $this->assertSame(
                '1.5',
                $this->locale->formatNumber(1.5),
            );

            // And more digits, to be sure it's actually counting, not defaulting.
            $this->assertSame(
                '1.005',
                $this->locale->formatNumber(1.005),
            );
        } finally {
            // tearDown() also restores, but keep the test self-contained.
            setlocale(LC_ALL, $this->original_locale);
        }
    }

    #[Test]
    public function testFormatNumberAutoDetectsPrecisionForCommaDecimalLocale(): void
    {
        try {
            $locale = SwatI18NLocale::get(self::DE_DE);
        } catch (SwatException $e) {
            $this->markTestSkipped('No comma-decimal locale available.');
        }

        // 1.5 has one fractional digit; the comma-decimal locale should render
        // it as "1,5" — proving precision detection is independent of the
        // object's own decimal separator (PHP stringifies floats with '.').
        $this->assertSame(
            '1,5',
            $locale->formatNumber(1.5)
        );
    }

    #[Test]
    public function testFormatCurrencyNational(): void
    {
        $this->assertSame(
            '$1,234.56',
            $this->locale->formatCurrency(1234.56),
        );
    }

    #[Test]
    public function testFormatCurrencyNegative(): void
    {
        $this->assertSame(
            '-$1,234.56',
            $this->locale->formatCurrency(-1234.56),
        );
    }

    #[Test]
    public function testFormatCurrencyZero(): void
    {
        $this->assertSame(
            '$0.00',
            $this->locale->formatCurrency(0),
        );
    }

    #[Test]
    public function testFormatCurrencyWithInvalidOverrideThrowsException(): void
    {
        $this->expectException(SwatException::class);

        $this->locale->formatCurrency(
            1,
            false,
            ['bogus_property' => 'x']
        );
    }

    #[Test]
    public function testParseFloat(): void
    {
        $this->assertSame(
            1234.56,
            $this->locale->parseFloat('1,234.56')
        );
    }

    #[Test]
    public function testParseFloatWithoutGroupings(): void
    {
        // Values do not have to be perfectly formatted to parse.
        $this->assertSame(
            1000.0,
            $this->locale->parseFloat('1000')
        );
    }

    #[Test]
    public function testParseFloatNegative(): void
    {
        $this->assertSame(
            -1234.56,
            $this->locale->parseFloat('-1,234.56')
        );
    }

    #[Test]
    public function testParseFloatInvalidReturnsNull(): void
    {
        $this->assertNull(
            $this->locale->parseFloat('not a number')
        );
    }

    #[Test]
    public function testParseFloatRoundTrip(): void
    {
        $formatted = $this->locale->formatNumber(98765.43, 2);
        $this->assertSame(
            98765.43,
            $this->locale->parseFloat($formatted)
        );
    }

    #[Test]
    public function testParseInteger(): void
    {
        $this->assertSame(
            1234,
            $this->locale->parseInteger('1,234')
        );
    }

    #[Test]
    public function testParseIntegerNegative(): void
    {
        $this->assertSame(
            -1234,
            $this->locale->parseInteger('-1,234')
        );
    }

    #[Test]
    public function testParseIntegerInvalidReturnsNull(): void
    {
        $this->assertNull(
            $this->locale->parseInteger('not a number')
        );
    }

    #[Test]
    public function testParseIntegerOverflowThrowsException(): void
    {
        $this->expectException(SwatIntegerOverflowException::class);

        // A value clearly larger than PHP_INT_MAX.
        $big = '999,999,999,999,999,999,999,999';
        $this->locale->parseInteger($big);
    }

    #[Test]
    public function testParseIntegerUnderflowThrowsException(): void
    {
        $this->expectException(SwatIntegerOverflowException::class);

        $small = '-999,999,999,999,999,999,999,999';
        $this->locale->parseInteger($small);
    }

    #[Test]
    public function testParseCurrency(): void
    {
        $this->assertSame(
            1234.56,
            $this->locale->parseCurrency('$1,234.56')
        );
    }

    #[Test]
    public function testParseCurrencyNegative(): void
    {
        $this->assertSame(
            -1234.56,
            $this->locale->parseCurrency('-$1,234.56'),
        );
    }

    #[Test]
    public function testParseCurrencyInvalidReturnsNull(): void
    {
        $this->assertNull(
            $this->locale->parseCurrency('not money')
        );
    }

    #[Test]
    public function testParseCurrencyRoundTrip(): void
    {
        $formatted = $this->locale->formatCurrency(4321.99);
        $this->assertSame(
            4321.99,
            $this->locale->parseCurrency($formatted)
        );
    }

    #[Test]
    public function testSetlocaleReturnsCurrent(): void
    {
        $current = SwatI18NLocale::setlocale(LC_ALL, '0');
        $this->assertIsString($current);
    }

    #[Test]
    public function testSetlocaleInvalidReturnsFalse(): void
    {
        $result = SwatI18NLocale::setlocale(
            LC_ALL,
            'this_is_not_a_real_locale.bogus',
        );

        $this->assertFalse($result);
    }

    #[Test]
    public function testSetAndReset(): void
    {
        // Force a known starting locale.
        SwatI18NLocale::setlocale(LC_NUMERIC, 'C');
        $before = setlocale(LC_NUMERIC, '0');

        $this->locale->set(LC_NUMERIC);
        $during = setlocale(LC_NUMERIC, '0');

        // The numeric locale should have changed after set().
        $this->assertNotSame($before, $during);

        $this->locale->reset(LC_NUMERIC);
        $after = setlocale(LC_NUMERIC, '0');

        // reset() should return the locale to its previous value.
        $this->assertSame($before, $after);
    }

    #[Test]
    public function testRoundToEven(): void
    {
        $this->assertSame(2.0, $this->locale->roundToEven(2.5, 0));
        $this->assertSame(4.0, $this->locale->roundToEven(3.5, 0));
        $this->assertSame(2.46, $this->locale->roundToEven(2.455, 2));
    }
}
