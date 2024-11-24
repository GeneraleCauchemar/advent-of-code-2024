<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2022;

use App\ConundrumSolver\AbstractConundrumSolver;

// --- Day 15: Beacon Exclusion Zone ---
class Day15ConundrumSolver extends AbstractConundrumSolver
{
    private array $sensors = [];
    private array $beacons = [];

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        return $this->findOccupiedSpotsForY($this->isTestMode() ? 10 : 2000000);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        return self::UNDETERMINED;
    }

    ////////////////
    // METHODS
    ////////////////

    private function findOccupiedSpotsForY(int $y): int
    {
        $this->sensors = [];
        $this->beacons = [];
        $ranges = [];

        foreach ($this->getInput() as $key => $line) {
            preg_match(
                '/(?<sensor_x>[-\d]+)[^-\d]+(?<sensor_y>[-\d]+)[^-\d]+(?<beacon_x>[-\d]+)[^-\d]+(?<beacon_y>[-\d]+)/',
                $line,
                $coords
            );

            $this->sensors[$key] = [(int) $coords['sensor_x'], (int) $coords['sensor_y']];
            $this->beacons[$key] = [(int) $coords['beacon_x'], (int) $coords['beacon_y']];
        }

        foreach ($this->sensors as $key => $sensor) {
            $manhattan = $this->getManhattan($sensor[0], $sensor[1], $this->beacons[$key][0], $this->beacons[$key][1]);
            $diffInY = $y - $sensor[1];
            $remainder = $manhattan - abs($diffInY);

            // Distance trop importante, y est
            // en dehors du cercle de détection
            if (0 > $remainder) {
                continue;
            }

            $x = $sensor[0] + $remainder;
            $diffInX = $sensor[0] - $x;
            $ranges[] = [($x - (abs($diffInX) * 2)), $x];

            unset($manhattan, $diffInY, $remainder, $x, $diffInX);
        }

        $this->beacons = array_unique($this->beacons, SORT_REGULAR);
        $tmp = [];

        foreach ($ranges as [$from, $to]) {
            $tmp = array_unique(array_merge($tmp, range($from, $to)));

            foreach ($this->beacons as $beacon) {
                if ($y === $beacon[1] && \in_array($beacon[0], range($from, $to), true)) {
                    unset($tmp[array_search($beacon[0], $tmp, true)]);
                }
            }
        }

        return \count($tmp);
    }

    private function getManhattan(int $fromX, int $fromY, int $toX, int $toY): int
    {
        return abs($fromX - $toX) + abs($fromY - $toY);
    }
}
