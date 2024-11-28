<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2022;

use App\ConundrumSolver\AbstractConundrumSolver;

// --- Day 6: Tuning Trouble ---
// PART ONE: 1760, PART TWO: 2974
class Day06ConundrumSolver extends AbstractConundrumSolver
{
    public function __construct()
    {
        parent::__construct('2022', '06', keepAsString: true);
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        return $this->solve(4);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        return $this->solve(14);
    }

    ////////////////
    // METHODS
    ////////////////

    private function solve(int $markerLength): string
    {
        $buffer = [];

        foreach (str_split($this->getInput()) as $index => $letter) {
            $buffer[++$index] = $letter;

            if ($markerLength === \count($buffer)) {
                // Every character is unique, return index
                if ($buffer === array_unique($buffer)) {
                    return (string) $index;
                }

                // Removes first element from array without re-indexing
                unset($buffer[array_key_first($buffer)]);
            }
        }

        return '';
    }
}
