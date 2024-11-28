<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2022;

use App\ConundrumSolver\AbstractConundrumSolver;

// --- Day 1: Calorie Counting ---
// PART ONE: 68292, PART TWO: 203203
class Day01ConundrumSolver extends AbstractConundrumSolver
{
    private array $caloriesPerElf = [];

    public function __construct()
    {
        parent::__construct('2022', '01', PHP_EOL . PHP_EOL);
    }

    ////////////////
    // PART 1
    ////////////////

    #[\Override]
    public function partOne(): string|int
    {
        foreach ($this->getInput() as $data) {
            $this->caloriesPerElf[] = array_map('\intval', explode(PHP_EOL, (string) $data));
        }

        array_walk($this->caloriesPerElf, static function (&$value): void {
            $value = array_sum($value);
        });

        return max($this->caloriesPerElf);
    }

    ////////////////
    // PART 2
    ////////////////

    #[\Override]
    public function partTwo(): string|int
    {
        arsort($this->caloriesPerElf);

        return array_sum(\array_slice($this->caloriesPerElf, 0, 3));
    }
}
