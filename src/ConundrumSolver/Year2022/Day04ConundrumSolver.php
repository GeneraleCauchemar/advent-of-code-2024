<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2022;

use App\ConundrumSolver\AbstractConundrumSolver;

// --- Day 4: Camp Cleanup ---
// PART ONE: 518, PART TWO: 909
class Day04ConundrumSolver extends AbstractConundrumSolver
{
    private array $spreadRanges;

    public function __construct(string $year, string $day)
    {
        parent::__construct($year, $day);
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        $this->setSpreadRanges();

        // Finds out every case where one range fully contains the other
        $output = array_filter($this->spreadRanges, fn($value): bool => \in_array(
            $this->getOverlap(...$value),
            $value,
            true
        ));

        return \count($output);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        // Finds out every case where the two ranges overlap
        $output = array_filter($this->spreadRanges, fn($value): bool => 0 < \count($this->getOverlap(...$value)));

        return \count($output);
    }

    ////////////////
    // METHODS
    ////////////////

    private function setSpreadRanges(): void
    {
        $this->spreadRanges = array_map(
            static fn($value): array => array_map(
                static fn($range): array => range(...array_map('\intval', explode('-', (string) $range))),
                explode(',', (string) $value)
            ),
            $this->getInput()
        );
    }

    private function getOverlap(array $range1, array $range2): array
    {
        return array_values(array_intersect($range1, $range2));
    }
}
