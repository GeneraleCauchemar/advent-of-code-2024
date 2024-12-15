<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;

/**
 * ❄️ Day 1: Historian Hysteria ❄️
 *
 * @see https://adventofcode.com/2024/day/1
 */
final class Day01ConundrumSolver extends AbstractConundrumSolver
{
    private array $left = [];
    private array $right = [];

    public function __construct()
    {
        parent::__construct('2024', '01');
    }

    #[\Override]
    public function warmup(): void
    {
        foreach ($this->getInput() as $line) {
            preg_match('/(?<left>\d+)\s+(?<right>\d+)/', $line, $matches);

            if (isset($matches['left'], $matches['right'])) {
                $this->left[] = (int) $matches['left'];
                $this->right[] = (int) $matches['right'];
            }
        }

        sort($this->left);
        sort($this->right);
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        $output = [];

        foreach ($this->left as $key => $value) {
            $output[] = abs($this->right[$key] - $value);
        }

        return array_sum($output);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        $output = [];
        $countedValues = array_count_values($this->right);

        foreach ($this->left as $value) {
            if (\array_key_exists($value, $countedValues)) {
                $output[] = $value * $countedValues[$value];
            }
        }

        return array_sum($output);
    }

    ////////////////
    // METHODS
    ////////////////
}
