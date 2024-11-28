<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2023;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Year2023\Day09\History;
use App\Entity\Year2023\Day09\Sequence;

// /// Day 9: Mirage Maintenance ///
class Day09ConundrumSolver extends AbstractConundrumSolver
{
    private array $firstValues = [];
    private array $lastValues = [];

    public function __construct()
    {
        parent::__construct('2023', '09');
    }

    #[\Override]
    public function warmup(): void
    {
        foreach ($this->getInput() as $line) {
            $history = new History();

            $history->computeSequences(array_map('\intval', explode(' ', (string) $line)));

            $firstValue = $lastValue = 0;

            /** @var Sequence $sequence */
            foreach ($history->sequences as $sequence) {
                $sequence->lastValue += $lastValue;
                $lastValue = $sequence->lastValue;

                $sequence->firstValue -= $firstValue;
                $firstValue = $sequence->firstValue;
            }

            $firstSequence = end($history->sequences);
            $this->lastValues[] = $firstSequence->lastValue;
            $this->firstValues[] = $firstSequence->firstValue;
        }
    }

    ////////////////
    // PART 1
    ////////////////

    #[\Override]
    public function partOne(): string|int
    {
        return array_sum($this->lastValues);
    }

    ////////////////
    // PART 2
    ////////////////

    #[\Override]
    public function partTwo(): string|int
    {
        return array_sum($this->firstValues);
    }
}
