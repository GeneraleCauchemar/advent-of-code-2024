<?php

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Year2024\Day10\DomainLogic;
use App\Entity\Year2024\Day10\Position;

// ❄️ Day 10: Hoof It ❄️
// @see https://adventofcode.com/2024/day/10
final class Day10ConundrumSolver extends AbstractConundrumSolver
{
    private array $startingPoints = [];
    private DomainLogic $domainLogic;

    public function __construct()
    {
        parent::__construct('2024', '10');
    }

    public function warmup(): void
    {
        $map = $this->map();
        $this->domainLogic = new DomainLogic($map);
    }

    // //////////////
    // PART 1
    // //////////////

    public function partOne(): string|int
    {
        $trails = $this->computeTrails();

        return $this->computeResults($trails);
    }

    // //////////////
    // PART 2
    // //////////////

    public function partTwo(): string|int
    {
        $trails = $this->computeTrails(true);

        return $this->computeResults($trails);
    }

    // //////////////
    // METHODS
    // //////////////

    private function map(): array
    {
        $map = [];

        foreach ($this->getInput() as $y => $line) {
            $row = [];

            foreach (str_split((string) $line) as $x => $height) {
                $position = new Position($y, $x, (int) $height);
                $row[$x] = $position;

                if (Position::START === (int) $height) {
                    $this->startingPoints[] = $position;
                }
            }

            $map[$y] = $row;
        }

        return $map;
    }

    private function computeTrails(bool $isPartTwo = false): array
    {
        $result = array_fill_keys(array_keys($this->startingPoints), []);

        foreach ($this->startingPoints as $key => $startingPoint) {
            $trails = [];
            $next = $this->domainLogic->getAdjacentNodes($startingPoint);

            foreach ($next as $position) {
                $this->getTrailsFrom($position, $trails, $isPartTwo);
                $result[$key] = $trails;
            }
        }

        return $result;
    }

    private function getTrailsFrom(Position $position, array &$trails, bool $isPartTwo = false): void
    {
        if (Position::END === $position->height) {
            if ($isPartTwo || !\in_array($position, $trails)) {
                $trails[] = $position;
            }

            return;
        }

        $next = $this->domainLogic->getAdjacentNodes($position);
        if (empty($next)) {
            return;
        }

        foreach ($next as $nextPosition) {
            $this->getTrailsFrom($nextPosition, $trails, $isPartTwo);
        }
    }

    private function computeResults(array $trails): float|int
    {
        $result = [];
        foreach ($trails as $trail) {
            $result[] = \count($trail);
        }

        return array_sum($result);
    }
}
