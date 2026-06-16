<?php

/**
 * Information for formatting numeric values.
 *
 * @copyright 2007-2026 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see       SwatLocale::formatNumber()
 * @see       SwatLocale::getNumberFormat()
 *
 * @phpstan-type TOverrideableNumberProperties array{
 *      decimal_separator?: string,
 *      thousands_separator?: string,
 *      grouping?: list<int>,
 *  }
 *
 * @phpstan-import-type LocaleConvArray from SwatI18NLocale
 */
class SwatI18NNumberFormat
{
    /**
     * Decimal point character.
     */
    public string $decimal_separator = '.';

    /**
     * Thousands separator.
     */
    public string $thousands_separator = '';

    /**
     * Numeric groupings.
     *
     * @var list<int>
     */
    public array $grouping = [];

    /**
     * Gets a new number format object with certain properties overridden from
     * specified values.
     *
     * The override information is specified as an associative array with
     * array keys representing property names of this formatting object and
     * array values being the overridden values.
     *
     * For example, to override the positive and negative signs of this format:
     *
     * ```php
     * $format->override([
     *      'n_sign' => 'neg',
     *      'p_sign' => 'pos'
     * ]);
     * ```
     *
     * @param TOverrideableNumberProperties $format the format information with which to
     *                                              override this format
     *
     * @return static a copy of this number format with the
     *                specified properties set to the new values
     *
     * @throws SwatException if any of the array keys do not match a formatting
     *                       property of this property
     */
    public function override(array $format): static
    {
        foreach ($format as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new SwatException(
                    'Number formatting information '
                        . "contains invalid property {$key} and cannot override "
                        . 'this number format.',
                );
            }
        }

        $new_format = clone $this;

        foreach ($format as $key => $value) {
            if ($value !== null) {
                $new_format->{$key} = $value;
            }
        }

        return $new_format;
    }
}
