<?php

declare(strict_types=1);

/**
 * Data providers for SwatNumber::ordinal() tests.
 */
final class OrdinalProvider
{
    /**
     * en_US (US English) ordinal case tests.
     *
     * @return Generator<string, array{float|int, string}>
     */
    public static function ordinalCasesEnUs(): Generator
    {
        yield from [
            // Zero case
            'zero' => [0, '0th'],

            // Basic positive cases
            'positive 1' => [1, '1st'],
            'positive 2' => [2, '2nd'],
            'positive 3' => [3, '3rd'],
            'positive 4' => [4, '4th'],

            // Positive teen exceptions
            'positive 11' => [11, '11th'],
            'positive 12' => [12, '12th'],
            'positive 13' => [13, '13th'],

            // Larger positive cases
            'positive 21'  => [21, '21st'],
            'positive 22'  => [22, '22nd'],
            'positive 23'  => [23, '23rd'],
            'positive 101' => [101, '101st'],
            'positive 111' => [111, '111th'],

            // Basic negative cases
            // note that the sign is "\u{2212}", the MINUS symbol, not a hyphen
            'negative 1' => [-1, '−1st'],
            'negative 2' => [-2, '−2nd'],
            'negative 3' => [-3, '−3rd'],
            'negative 4' => [-4, '−4th'],

            // Negative teen exceptions
            'negative 11' => [-11, '−11th'],
            'negative 12' => [-12, '−12th'],
            'negative 13' => [-13, '−13th'],

            // Larger negative cases
            'negative 21'  => [-21, '−21st'],
            'negative 22'  => [-22, '−22nd'],
            'negative 23'  => [-23, '−23rd'],
            'negative 113' => [-113, '−113th'],
        ];
    }

    /**
     * fr_FR (French) ordinal case tests.
     *
     * @return Generator<string, array{float|int, string}>
     */
    public static function ordinalCasesFrFr(): Generator
    {
        yield from [
            // Zero case
            'zero' => [0, '0e'],

            // Positive cases
            'positive 1'   => [1, '1er'],
            'positive 2'   => [2, '2e'],
            'positive 3'   => [3, '3e'],
            'positive 4'   => [4, '4e'],
            'positive 11'  => [11, '11e'],
            'positive 12'  => [12, '12e'],
            'positive 13'  => [13, '13e'],
            'positive 21'  => [21, '21e'],
            'positive 101' => [101, '101e'],

            // Negative cases
            'negative 1'   => [-1, '−1er'],
            'negative 2'   => [-2, '−2e'],
            'negative 3'   => [-3, '−3e'],
            'negative 4'   => [-4, '−4e'],
            'negative 11'  => [-11, '−11e'],
            'negative 12'  => [-12, '−12e'],
            'negative 13'  => [-13, '−13e'],
            'negative 21'  => [-21, '−21e'],
            'negative 113' => [-101, '−101e'],
        ];
    }
}
