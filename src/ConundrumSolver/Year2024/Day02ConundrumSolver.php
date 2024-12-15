<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;

/**
 * ❄️ Day 2: Red-Nosed Reports ❄️
 *
 * @see https://adventofcode.com/2024/day/2
 */
final class Day02ConundrumSolver extends AbstractConundrumSolver
{
    public function __construct()
    {
        parent::__construct('2024', '02');
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        $areSafe = 0;

        foreach ($this->getInput() as $report) {
            $levels = $this->getLevels($report);

            if (!$this->areIncreasingOrDecreasing($levels)) {
                continue;
            }

            $areSafe += $this->incrementsAreSafe($levels) ? 1 : 0;
        }

        return $areSafe;
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        $areSafe = 0;

        foreach ($this->getInput() as $report) {
            $levels = $this->getLevels($report);
            $safe = $this->areSafe($levels);

            if (!$safe) {
                for ($i = 0, $iMax = \count($levels); $i < $iMax; $i++) {
                    $tmp = $levels;
                    array_splice($tmp, $i, 1);

                    if ($this->areSafe($tmp)) {
                        $safe = true;

                        break;
                    }
                }
            }

            if ($safe) {
                $areSafe++;
            }
        }

        return $areSafe;
    }

    ////////////////
    // METHODS
    ////////////////

    private function getLevels(string $report): array
    {
        return array_map('\intval', explode(' ', $report));
    }

    private function areSafe(array $values): bool
    {
        return $this->areIncreasingOrDecreasing($values) && $this->incrementsAreSafe($values);
    }

    private function areIncreasingOrDecreasing(array $values): bool
    {
        $decreasing = $increasing = $values;

        sort($increasing);
        rsort($decreasing);

        return $increasing === $values || $decreasing === $values;
    }

    private function incrementsAreSafe(
        array $values,
    ): bool {
        $carry = null;

        foreach ($values as $level) {
            if (null !== $carry && !$this->isDiffOk($level, $carry)) {
                return false;
            }

            $carry = $level;
        }

        return true;
    }

    private function isDiffOk(int $a, int $b): bool
    {
        $diff = abs($a - $b);

        return 1 <= $diff && 3 >= $diff;
    }
}
