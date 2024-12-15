<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2019;

use App\ConundrumSolver\AbstractConundrumSolver;

/**
 * ❄️ Day 2: 1202 Program Alarm ❄️
 *
 * @see https://adventofcode.com/2019/day/2
 */
final class Day02ConundrumSolver extends AbstractConundrumSolver
{
    private array $fuelRequirements = [];

    public function __construct()
    {
        parent::__construct('2019', '02');
    }

    #[\Override]
    public function warmup(): void
    {
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        $program = array_map(static fn($value) => (int) $value, explode(',', $this->getInput()[0]));

        if (!$this->testMode) {
            $program[1] = 12;
            $program[2] = 2;
        }

        $iMax = floor(\count($program) / 4);

        for ($i = 0; $i < $iMax; $i++) {
            $j = $i * 4;

            if (99 === $program[$j]) {
                break; // Halt (and catch fire)
            }

            $position = $program[$j + 3];
            $values = [$program[$program[$j + 1]], $program[$program[$j + 2]]];
            $result = match ($program[$j]) {
                1 => array_sum($values),
                2 => array_product($values),
            };

            $program[$position] = $result;
        }

        return $program[0];
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

}
