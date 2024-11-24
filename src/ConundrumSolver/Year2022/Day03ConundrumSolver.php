<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2022;

use App\ConundrumSolver\AbstractConundrumSolver;

// --- Day 3: Rucksack Reorganization ---
// PART ONE: 7701, PART TWO: 2644
class Day03ConundrumSolver extends AbstractConundrumSolver
{
    public function __construct(string $year, string $day)
    {
        parent::__construct($year, $day);
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        // For each rucksack, finds the common item and returns its value
        $output = array_map(function ($value): int {
            $halved = str_split($value, \strlen($value) / 2);
            $common = array_intersect(
                str_split($halved[0]),
                str_split($halved[1])
            );

            return $this->getNumericValueForLetter(reset($common));
        }, $this->getInput());

        return array_sum($output);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        // For each group of three elves, finds the common item and returns its value
        $output = array_map(function ($value): int {
            $badge = array_intersect(
                str_split((string) $value[0]),
                str_split((string) $value[1]),
                str_split((string) $value[2]),
            );

            return $this->getNumericValueForLetter(reset($badge));
        }, array_chunk($this->getInput(), 3));

        return array_sum($output);
    }

    ////////////////
    // METHODS
    ////////////////

    // Compute the value using the ASCII table
    private function getNumericValueForLetter(string $letter): int
    {
        return \ord($letter) - (ctype_lower($letter) ? 96 : 38);
    }
}
