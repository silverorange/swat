<?php

/**
 * Data providers for SwatI18NLocale::roundToEven() tests.
 */
class SwatI18NRoundingProvider
{
    /**
     * Core halfway cases at precision 0.
     * Halves round toward the nearest EVEN integer.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function halfwayCasesPrecisionZeroProvider(): Generator
    {
        yield 'p0: 0.5 down to even (0)' => [0.5, 0, 0.0];
        yield 'p0: 1.5 up to even (2)' => [1.5, 0, 2.0];
        yield 'p0: 2.5 down to even (2)' => [2.5, 0, 2.0];
        yield 'p0: 3.5 up to even (4)' => [3.5, 0, 4.0];
        yield 'p0: 4.5 down to even (4)' => [4.5, 0, 4.0];
        yield 'p0: -0.5 to even (0)' => [-0.5, 0, 0.0];
        yield 'p0: -1.5 to even (-2)' => [-1.5, 0, -2.0];
        yield 'p0: -2.5 to even (-2)' => [-2.5, 0, -2.0];
        yield 'p0: -3.5 to even (-4)' => [-3.5, 0, -4.0];
    }

    /**
     * Halfway cases at precision 1.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function halfwayCasesPrecisionOneProvider(): Generator
    {
        yield 'p1: 0.25 to even (0.2)' => [0.25, 1, 0.2];
        yield 'p1: 0.35 to even (0.4)' => [0.35, 1, 0.4];
        yield 'p1: 0.45 to even (0.4)' => [0.45, 1, 0.4];
        yield 'p1: 0.55 to even (0.6)' => [0.55, 1, 0.6];
        yield 'p1: 1.15 to even (1.2)' => [1.15, 1, 1.2];
        yield 'p1: 1.25 to even (1.2)' => [1.25, 1, 1.2];
    }

    /**
     * Halfway cases at precision 2.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function halfwayCasesPrecisionTwoProvider(): Generator
    {
        yield 'p2: 0.125 to even (0.12)' => [0.125, 2, 0.12];
        yield 'p2: 0.135 to even (0.14)' => [0.135, 2, 0.14];
        yield 'p2: 2.345 to even (2.34)' => [2.345, 2, 2.34];
        yield 'p2: 2.355 to even (2.36)' => [2.355, 2, 2.36];
    }

    /**
     * Non-halfway values fall back to standard nearest rounding.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function nonHalfwayCasesProvider(): Generator
    {
        yield 'p0: 0.4 down' => [0.4, 0, 0.0];
        yield 'p0: 0.6 up' => [0.6, 0, 1.0];
        yield 'p0: 2.4 down' => [2.4, 0, 2.0];
        yield 'p0: 2.6 up' => [2.6, 0, 3.0];
        yield 'p1: 1.23 down' => [1.23, 1, 1.2];
        yield 'p1: 1.27 up' => [1.27, 1, 1.3];
        yield 'p0: -2.4 down' => [-2.4, 0, -2.0];
        yield 'p0: -2.6 up' => [-2.6, 0, -3.0];
    }

    /**
     * Integer inputs must be accepted and returned as floats.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function integerInputProvider(): Generator
    {
        yield 'int: 1 at p0' => [1, 0, 1.0];
        yield 'int: 2 at p0' => [2, 0, 2.0];
        yield 'int: 100 at p2' => [100, 2, 100.0];
        yield 'int: 0 at p0' => [0, 0, 0.0];
        yield 'int: -5 at p0' => [-5, 0, -5.0];
    }

    /**
     * Edge cases involving zero and already-rounded values.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function edgeCaseProvider(): Generator
    {
        yield 'edge: 0.0 at p0' => [0.0, 0, 0.0];
        yield 'edge: -0.0 at p0' => [-0.0, 0, 0.0];
        yield 'edge: 1.0 at p0' => [1.0, 0, 1.0];
        yield 'edge: 100.0 at p2' => [100.0, 2, 100.0];
        yield 'edge: 0.0 at p5' => [0.0, 5, 0.0];
    }

    /**
     * Negative precision rounds to tens, hundreds, etc.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function negativePrecisionProvider(): Generator
    {
        yield 'pm1: 25 to even tens (20)' => [25.0, -1, 20.0];
        yield 'pm1: 35 to even tens (40)' => [35.0, -1, 40.0];
        yield 'pm2: 150 to even hundreds' => [150.0, -2, 200.0];
        yield 'pm2: 250 to even hundreds' => [250.0, -2, 200.0];
    }

    /**
     * Values that are inexact in IEEE-754 binary floating point.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function floatingPointTrapProvider(): Generator
    {
        yield 'fp: 1.005 stored below half' => [1.005, 2, 1.00];
        yield 'fp: 2.675 classic bug' => [2.675, 2, 2.68];
        yield 'fp: 0.1 inexact' => [0.1, 1, 0.1];
        yield 'fp: 8.075 edge' => [8.075, 2, 8.08];
    }
}
