<?php

declare(strict_types=1);

/**
 * Data providers for SwatNumber::roundUp() tests.
 */
final class RoundUpProvider
{
    /**
     * Core halfway cases at precision 0.
     * Halves round AWAY FROM ZERO.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function halfwayCasesPrecisionZero(): Generator
    {
        yield from [
            'p0: 0.5 up to 1' => [0.5, 0, 1.0],
            'p0: 1.5 up to 2' => [1.5, 0, 2.0],
            'p0: 2.5 up to 3' => [2.5, 0, 3.0],
            'p0: 3.5 up to 4' => [3.5, 0, 4.0],
            'p0: 4.5 up to 5' => [4.5, 0, 5.0],
            'p0: -0.5 to -1'  => [-0.5, 0, -1.0],
            'p0: -1.5 to -2'  => [-1.5, 0, -2.0],
            'p0: -2.5 to -3'  => [-2.5, 0, -3.0],
            'p0: -3.5 to -4'  => [-3.5, 0, -4.0],
        ];
    }

    /**
     * Halfway cases at precision 1.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function halfwayCasesPrecisionOne(): Generator
    {
        yield from [
            'p1: 0.25 up to 0.3' => [0.25, 1, 0.3],
            'p1: 0.35 up to 0.4' => [0.35, 1, 0.4],
            'p1: 0.45 up to 0.5' => [0.45, 1, 0.5],
            'p1: 0.55 up to 0.6' => [0.55, 1, 0.6],
            'p1: 1.15 up to 1.2' => [1.15, 1, 1.2],
            'p1: 1.25 up to 1.3' => [1.25, 1, 1.3],
            'p1: -0.25 to -0.3'  => [-0.25, 1, -0.3],
            'p1: -1.25 to -1.3'  => [-1.25, 1, -1.3],
        ];
    }

    /**
     * Halfway cases at precision 2.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function halfwayCasesPrecisionTwo(): Generator
    {
        yield from [
            'p2: 0.125 up to 0.13' => [0.125, 2, 0.13],
            'p2: 0.135 up to 0.14' => [0.135, 2, 0.14],
            'p2: 2.345 up to 2.35' => [2.345, 2, 2.35],
            'p2: 2.355 up to 2.36' => [2.355, 2, 2.36],
        ];
    }

    /**
     * Non-halfway values round to the nearest, same as any mode.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function nonHalfwayCases(): Generator
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
    public static function integerInput(): Generator
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
    public static function edgeCases(): Generator
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
    public static function negativePrecision(): Generator
    {
        yield from [
            'pm1: 25 up to 30'   => [25.0, -1, 30.0],
            'pm1: 35 up to 40'   => [35.0, -1, 40.0],
            'pm2: 150 up to 200' => [150.0, -2, 200.0],
            'pm2: 250 up to 300' => [250.0, -2, 300.0],
        ];
    }

    /**
     * Values that are inexact in IEEE-754 binary floating point.
     *
     * NOTE: These expectations document the contract of THIS
     * implementation. A string-based strategy may differ from
     * an arithmetic one — revisit if the strategy changes.
     *
     * @return Generator<string, array{float|int, int, float}>
     */
    public static function floatingPointTraps(): Generator
    {
        yield from [
            'fp: 1.005 stored below half' => [1.005, 2, 1.01],
            'fp: 2.675 classic bug'       => [2.675, 2, 2.68],
            'fp: 0.1 inexact'             => [0.1, 1, 0.1],
            'fp: 8.075 edge'              => [8.075, 2, 8.08],
        ];
    }
}
