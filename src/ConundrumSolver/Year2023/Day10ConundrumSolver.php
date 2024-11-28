<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2023;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Year2023\Day10\DomainLogic;
use App\Entity\Year2023\Day10\Position;
use App\Entity\Year2023\Day10\TerrainCost;
use JMGQ\AStar\AStar;

// /// Day Day 10: Pipe Maze ///
class Day10ConundrumSolver extends AbstractConundrumSolver
{
    private Position $start;
    private array $positions = [];
    private array $loop = [];

    public function __construct()
    {
        parent::__construct('2023', '10');
    }

    #[\Override]
    public function warmup(): void
    {
        foreach ($this->getInput(self::PART_TWO) as $keyY => $line) {
            $y = [];

            foreach (str_split((string) $line) as $keyX => $pipe) {
                $position = new Position($keyY, $keyX, $pipe);

                if (Position::START === $pipe) {
                    $this->start = $position;
                }

                $y[$keyX] = $position;
            }

            $this->positions[$keyY] = $y;
        }

        $terrainCost = new TerrainCost($this->positions);
        $domainLogic = new DomainLogic($terrainCost);
        $aStar = new AStar($domainLogic);

        $solution = $aStar->run(...$this->findStartAndEnd($this->positions));
        $this->loop = array_merge(
            [$this->start],
            $solution,
            [$this->start]
        );
    }

    ////////////////
    // PART 1
    ////////////////

    #[\Override]
    public function partOne(): string|int
    {
        return (\count($this->loop) - 1) / 2;
    }

    ////////////////
    // PART 2
    ////////////////

    #[\Override]
    public function partTwo(): string|int
    {
        $inside = 0;

        foreach (array_merge(...$this->positions) as $position) {
            if ($this->isPointInsideLoop($position, $this->loop)) {
                $inside++;
            }
        }

        return $inside;
    }

    ////////////////
    // METHODS
    ////////////////

    private function findStartAndEnd(array $positions): array
    {
        $starts = [];

        foreach ($positions as $line) {
            foreach ($line as $position) {
                if ($this->start->isAdjacentTo($position) && $position->isOrientedTowards($this->start)) {
                    $starts[] = $position;
                }
            }
        }

        return [$starts[0], $starts[1]];
    }

    private function isPointInsideLoop(Position $position, array $loop): bool
    {
        // Check if already a point on loop
        if (\in_array($position, $loop, true)) {
            return false;
        }

        $crossings = 0;

        for ($i = 1, $iMax = \count($loop); $i < $iMax; $i++) {
            /** @var Position $loopPointA */
            /** @var Position $loopPointB */
            $loopPointA = $loop[$i - 1];
            $loopPointB = $loop[$i];

            // If both loop points are on different rows
            // and X of position is over min X of the two points and under or equal max X of the two points
            // and Y of position is under or equal max Y of the two points
            if (
                $loopPointA->row !== $loopPointB->row
                && min($loopPointA->row, $loopPointB->row) < $position->row
                && max($loopPointA->row, $loopPointB->row) >= $position->row
                && max($loopPointA->column, $loopPointB->column) >= $position->column
            ) {
                $xinters = ($position->row - $loopPointA->row)
                    * ($loopPointB->column - $loopPointA->column)
                    / ($loopPointB->row - $loopPointA->row)
                    + $loopPointA->column;

                if ($position->column <= $xinters) {
                    $crossings++;
                }
            }
        }

        // Point is inside loop if a line drawn from it crosses the loop an
        // odd number of time
        return 0 !== $crossings % 2;
    }
}
