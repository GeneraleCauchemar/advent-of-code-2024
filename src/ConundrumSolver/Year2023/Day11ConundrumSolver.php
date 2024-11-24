<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2023;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Year2023\Day11\Position;

// /// Day 11: Cosmic Expansion ///
class Day11ConundrumSolver extends AbstractConundrumSolver
{
    private const int PART_ONE_EXPANSION_FACTOR = 2;
    private const int PART_TWO_EXPANSION_FACTOR = 1000000;

    private array $universe = [];
    private array $galaxiesBeforeExpansion = [];
    private array $galaxiesAfterExpansion = [];
    private array $pairs = [];

    public function __construct(string $year, string $day)
    {
        parent::__construct($year, $day);
    }

    #[\Override]
    public function warmup(): void
    {
        $this->mapBeforeExpansion();
        $this->expand();
        $this->pinpointGalaxies();
        $this->createPairs();
    }

    ////////////////
    // PART 1
    ////////////////

    #[\Override]
    public function partOne(): string|int
    {
        $sum = 0;

        foreach ($this->pairs as $pair) {
            $sum += $this->getManhattan(...$pair);
        }

        return $sum;
    }

    ////////////////
    // PART 2
    ////////////////

    #[\Override]
    public function partTwo(): string|int
    {
        $sum = 0;

        foreach ($this->pairs as $pair) {
            $pair = $this->computeNewPositions(...$pair);
            $sum += $this->getManhattan(...$pair);
        }

        return $sum;
    }

    ////////////////
    // METHODS
    ////////////////

    private function mapBeforeExpansion(): void
    {
        $i = 1;

        foreach ($this->getInput() as $row => $line) {
            $galaxies = array_filter(str_split((string) $line), static fn(string $symbol): bool => '#' === $symbol);

            foreach ($galaxies as $column => $galaxy) {
                $this->galaxiesBeforeExpansion[$i] = new Position($row, $column, $i);

                $i++;
            }
        }
    }

    private function expand(): void
    {
        // Expand lines then rotate to expand columns
        $input = $this->doExpand($this->getInput());
        $this->rotateMatrix($input);

        // Expand columns then rotate back
        $input = $this->doExpand($input);
        $this->rotateMatrix($input);

        $this->universe = $input;
    }

    private function doExpand(array $input): array
    {
        $expanded = [];

        foreach ($input as &$line) {
            $line = \is_string($line) ? str_split($line) : $line;
            $lines = array_fill(0, $this->getExpansionFactor($line), $line);
            $expanded = array_merge([], ...[$expanded, $lines]);
        }

        return $expanded;
    }

    private function getExpansionFactor(array $values): int
    {
        return $this->isOnlyEmptySpace($values) ? self::PART_ONE_EXPANSION_FACTOR : 1;
    }

    private function isOnlyEmptySpace(array $values): bool
    {
        $unique = array_unique($values);

        return 1 === \count($unique) && '.' === $unique[0];
    }

    private function rotateMatrix(array &$matrix): void
    {
        // Effectively, this NULL callback loops through all the arrays in parallel taking each
        // value from them in turn to build a new array:
        // @see https://stackoverflow.com/a/30082922
        $matrix = array_map(null, ...$matrix);
    }

    private function pinpointGalaxies(): void
    {
        $i = 1;

        foreach ($this->universe as $row => $line) {
            $galaxies = array_filter($line, static fn(string $symbol): bool => '#' === $symbol);

            foreach ($galaxies as $column => $galaxy) {
                $this->galaxiesAfterExpansion[$i] = new Position($row, $column, $i);

                $i++;
            }
        }
    }

    private function createPairs(): void
    {
        $max = \count($this->galaxiesAfterExpansion);

        for ($i = 0; $i < $max; $i++) {
            for ($j = $i + 1; $j < $max; $j++) {
                $this->pairs[] = [
                    $this->galaxiesAfterExpansion[$i + 1],
                    $this->galaxiesAfterExpansion[$j + 1],
                ];
            }
        }
    }

    private function getManhattan(Position $from, Position $to): int
    {
        return abs($from->column - $to->column) + abs($from->row - $to->row);
    }

    private function computeNewPositions(Position $from, Position $to): array
    {
        $beforeFrom = $this->galaxiesBeforeExpansion[$from->id];
        $beforeTo = $this->galaxiesBeforeExpansion[$to->id];

        return [
            new Position(
                $this->computeDiff($beforeFrom->row, $from->row),
                $this->computeDiff($beforeFrom->column, $from->column),
                $from->id
            ),
            new Position(
                $this->computeDiff($beforeTo->row, $to->row),
                $this->computeDiff($beforeTo->column, $to->column),
                $from->id
            ),
        ];
    }

    private function computeDiff(int $before, int $after): int
    {
        $diff = abs($after - $before);

        return $before + ($diff * self::PART_TWO_EXPANSION_FACTOR - $diff);
    }
}
