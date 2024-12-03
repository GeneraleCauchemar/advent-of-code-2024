<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2019;

use App\ConundrumSolver\AbstractConundrumSolver;

// /// Day 1: The Tyranny of the Rocket Equation ///
class Day01ConundrumSolver extends AbstractConundrumSolver
{
    private array $fuelRequirements = [];

    public function __construct()
    {
        parent::__construct('2019', '01');
    }

    #[\Override]
    public function warmup(): void
    {
        foreach ($this->getInput() as $moduleMass) {
            $this->fuelRequirements[$moduleMass] = $this->computeFuelRequirementForMass((int) $moduleMass);
        }
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        return array_sum($this->fuelRequirements);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        $additionalFuel = [];

        foreach ($this->fuelRequirements as $fuel) {
            while (0 < $fuel) {
                $fuel = $this->computeFuelRequirementForMass($fuel);
                $additionalFuel[] = max(0, $fuel);
            }
        }

        return array_sum($this->fuelRequirements) + array_sum($additionalFuel);
    }

    ////////////////
    // METHODS
    ////////////////

    private function computeFuelRequirementForMass(int $mass): int
    {
        return (int) floor($mass / 3) - 2;
    }
}
