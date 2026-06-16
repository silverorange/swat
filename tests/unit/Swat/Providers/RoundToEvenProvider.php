<?php

/**
 * Data providers for SwatNumber::roundToEven() tests.
 */
final class RoundToEvenProvider
{
    /**
     * Core halfway cases at precision 0.
     * Halves round toward the nearest EVEN integer.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function halfwayCasesPrecisionZeroProvider(): Generator
    {
        yield from [
            'p0: 0.5 down to even (0)' => [0.5, 0, 0.0],
            'p0: 1.5 up to even (2)'   => [1.5, 0, 2.0],
            'p0: 2.5 down to even (2)' => [2.5, 0, 2.0],
            'p0: 3.5 up to even (4)'   => [3.5, 0, 4.0],
            'p0: 4.5 down to even (4)' => [4.5, 0, 4.0],
            'p0: -0.5 to even (0)'     => [-0.5, 0, 0.0],
            'p0: -1.5 to even (-2)'    => [-1.5, 0, -2.0],
            'p0: -2.5 to even (-2)'    => [-2.5, 0, -2.0],
            'p0: -3.5 to even (-4)'    => [-3.5, 0, -4.0],
        ];
    }

    /**
     * Halfway cases at precision 1.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function halfwayCasesPrecisionOneProvider(): Generator
    {
        yield from [
            'p1: 0.25 to even (0.2)' => [0.25, 1, 0.2],
            'p1: 0.35 to even (0.4)' => [0.35, 1, 0.4],
            'p1: 0.45 to even (0.4)' => [0.45, 1, 0.4],
            'p1: 0.55 to even (0.6)' => [0.55, 1, 0.6],
            'p1: 1.15 to even (1.2)' => [1.15, 1, 1.2],
            'p1: 1.25 to even (1.2)' => [1.25, 1, 1.2],
        ];
    }

    /**
     * Halfway cases at precision 2.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function halfwayCasesPrecisionTwoProvider(): Generator
    {
        yield from [
            'p2: 0.125 to even (0.12)' => [0.125, 2, 0.12],
            'p2: 0.135 to even (0.14)' => [0.135, 2, 0.14],
            'p2: 2.345 to even (2.34)' => [2.345, 2, 2.34],
            'p2: 2.355 to even (2.36)' => [2.355, 2, 2.36],
        ];
    }

    /**
     * Non-halfway values fall back to standard nearest rounding.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function nonHalfwayCasesProvider(): Generator
    {
        yield from [
            'p0: 0.4 down'  => [0.4, 0, 0.0],
            'p0: 0.6 up'    => [0.6, 0, 1.0],
            'p0: 2.4 down'  => [2.4, 0, 2.0],
            'p0: 2.6 up'    => [2.6, 0, 3.0],
            'p1: 1.23 down' => [1.23, 1, 1.2],
            'p1: 1.27 up'   => [1.27, 1, 1.3],
            'p0: -2.4 down' => [-2.4, 0, -2.0],
            'p0: -2.6 up'   => [-2.6, 0, -3.0],
        ];
    }

    /**
     * Integer inputs must be accepted and returned as floats.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function integerInputProvider(): Generator
    {
        yield from [
            'int: 1 at p0'   => [1, 0, 1.0],
            'int: 2 at p0'   => [2, 0, 2.0],
            'int: 100 at p2' => [100, 2, 100.0],
            'int: 0 at p0'   => [0, 0, 0.0],
            'int: -5 at p0'  => [-5, 0, -5.0],
        ];
    }

    /**
     * Edge cases involving zero and already-rounded values.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function edgeCaseProvider(): Generator
    {
        yield from [
            'edge: 0.0 at p0'   => [0.0, 0, 0.0],
            'edge: -0.0 at p0'  => [-0.0, 0, 0.0],
            'edge: 1.0 at p0'   => [1.0, 0, 1.0],
            'edge: 100.0 at p2' => [100.0, 2, 100.0],
            'edge: 0.0 at p5'   => [0.0, 5, 0.0],
        ];
    }

    /**
     * Negative precision rounds to tens, hundreds, etc.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function negativePrecisionProvider(): Generator
    {
        yield from [
            'pm1: 25 to even tens (20)' => [25.0, -1, 20.0],
            'pm1: 35 to even tens (40)' => [35.0, -1, 40.0],
            'pm2: 150 to even hundreds' => [150.0, -2, 200.0],
            'pm2: 250 to even hundreds' => [250.0, -2, 200.0],
        ];
    }

    /**
     * Values that are inexact in IEEE-754 binary floating point.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function floatingPointTrapProvider(): Generator
    {
        yield from [
            'fp: 1.005 stored below half' => [1.005, 2, 1.00],
            'fp: 2.675 classic bug'       => [2.675, 2, 2.68],
            'fp: 0.1 inexact'             => [0.1, 1, 0.1],
            'fp: 8.075 edge'              => [8.075, 2, 8.08],
        ];
    }
}
