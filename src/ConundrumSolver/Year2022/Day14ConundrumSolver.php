<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2022;

use App\ConundrumSolver\AbstractConundrumSolver;

// --- Day 14: Regolith Reservoir ---
class Day14ConundrumSolver extends AbstractConundrumSolver
{
    private const array SAND_SOURCE = ['x' => 500, 'y' => 0];

    private array $cave = [];
    private int $endlessFloor = 0;
    private array $rockStructures = [];
    private int $sandUnits;

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        $this->initCave();
        $this->fillWithSand();

        return $this->sandUnits;
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        $this->initCave(self::PART_TWO);
        $this->fillWithSand(self::PART_TWO);

        $this->printCave();

        return $this->sandUnits;
    }

    ////////////////
    // METHODS
    ////////////////

    private function initCave(int $part = self::PART_ONE): void
    {
        $this->sandUnits = 0;
        $xs = [self::SAND_SOURCE['x']];
        $ys = [self::SAND_SOURCE['y']];

        foreach ($this->getInput() as $rockPath) {
            $points = explode(' -> ', $rockPath);

            for ($i = 0; $i < (\count($points) - 1); $i++) {
                [$xFrom, $yFrom] = explode(',', $points[$i]);
                [$xTo, $yTo] = explode(',', $points[$i + 1]);

                foreach (range((int) $yFrom, (int) $yTo) as $y) {
                    $ys[] = $y;

                    foreach (range((int) $xFrom, (int) $xTo) as $x) {
                        $this->rockStructures[$y][] = $x;
                        $xs[] = $x;
                    }
                }
            }
        }

        for ($y = min($ys); $y <= max($ys); $y++) {
            for ($x = min($xs); $x <= max($xs); $x++) {
                $this->cave[$y][$x] = $this->isRock($x, $y) ? '#' : '.';
            }
        }

        $this->cave[self::SAND_SOURCE['y']][self::SAND_SOURCE['x']] = '+';

        if (self::PART_TWO === $part) {
            for ($x = min($xs); $x <= max($xs); $x++) {
                $this->cave[max($ys) + 1][$x] = '.';
                $this->cave[max($ys) + 2][$x] = '#';
            }

            $this->endlessFloor = array_key_last($this->cave);
        }
    }

    private function isRock(int $x, int $y): bool
    {
        return \array_key_exists($y, $this->rockStructures) && \in_array($x, $this->rockStructures[$y], true);
    }

    private function fillWithSand(int $part = self::PART_ONE): void
    {
        $position = $this->moveSand(
            ['x' => self::SAND_SOURCE['x'], 'y' => self::SAND_SOURCE['y']],
            $part
        );

        if (null === $position) {
            return;
        }

        $this->cave[$position['y']][$position['x']] = 'o';
        $this->sandUnits++;

        $this->fillWithSand($part);
    }

    private function moveSand(array $position, int $part = self::PART_ONE): ?array
    {
        // Move down as far as possible
        for ($y = $position['y'], $yMax = \count($this->cave); $y < $yMax; $y++) {
            // Stopped at the bottom of Y
            if (!$this->isFree($position['x'], $y, $part)) {
                if ($part === self::PART_TWO && $this->isSandSource($position['x'], $y)) {
                    return null;
                }

                if (!$this->isSandSource($position['x'], $y)) {
                    $position = ['x' => $position['x'], 'y' => $y - 1];

                    break;
                }
            }

            // Last iteration, still not stopped, about to fall
            if ($part === self::PART_ONE && $y === \count($this->cave) - 1) {
                return null;
            }
        }

        if ($part === self::PART_ONE && $this->fallsIntoTheEndlessVoid($position['x'] - 1, $position['y'])) {
            return null;
        }

        if ($this->isFree($position['x'] - 1, $position['y'] + 1, $part)) {
            // Move down-left
            $position = $this->moveSand(['x' => $position['x'] - 1, 'y' => $position['y'] + 1], $part);
        }

        if (null === $position ||
            ($part === self::PART_ONE && $this->fallsIntoTheEndlessVoid($position['x'] + 1, $position['y']))) {
            return null;
        }

        if ($this->isFree($position['x'] + 1, $position['y'] + 1, $part)) {
            // Move down-right
            $position = $this->moveSand(['x' => $position['x'] + 1, 'y' => $position['y'] + 1], $part);
        }

        return $position;
    }

    private function fallsIntoTheEndlessVoid(int $x, int $y): bool
    {
        return !\array_key_exists($y, $this->cave) || !\array_key_exists($x, $this->cave[$y]);
    }

    private function isFree(int $x, int $y, int $part = self::PART_ONE): bool
    {
        if ($part === self::PART_TWO) {
            if ($y === $this->endlessFloor) {
                return false;
            }

            if (!\array_key_exists($x, $this->cave[$y])) {
                $this->extendCave($x);
            }
        }

        return '.' === $this->cave[$y][$x] || ($part === self::PART_TWO && '+' === $this->cave[$y][$x]);
    }

    private function isSandSource(int $x, int $y): bool
    {
        return $x === self::SAND_SOURCE['x'] && $y === self::SAND_SOURCE['y'];
    }

    private function extendCave(int $x): void
    {
        foreach ($this->cave as $y => $line) {
            $symbol = $this->endlessFloor === $y ? '#' : '.';

            if ($x < min(array_keys($this->cave[$y]))) {
                $this->cave[$y] = [$x => $symbol] + $this->cave[$y];
            } else {
                $this->cave[$y] += [$x => $symbol];
            }
        }
    }

    private function printCave(): void
    {
        foreach ($this->cave as $y) {
            echo implode('', $y) . PHP_EOL;
        }
    }
}
