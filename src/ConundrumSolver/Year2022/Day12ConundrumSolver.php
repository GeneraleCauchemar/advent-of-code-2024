<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2022;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Year2022\Day12\DomainLogic;
use App\Entity\Year2022\Day12\Position;
use App\Entity\Year2022\Day12\TerrainCost;
use JMGQ\AStar\AStar;

// --- Day 12: Hill Climbing Algorithm ---
class Day12ConundrumSolver extends AbstractConundrumSolver
{
    private const string START = 'S';
    private const string END = 'E';

    private DomainLogic $domainLogic;
    private AStar $aStar;
    private Position $start;
    private Position $end;
    private array $positions = [];

    public function __construct(string $year, string $day)
    {
        parent::__construct($year, $day);
    }

    #[\Override]
    public function warmup(): void
    {
        foreach ($this->getInput() as $keyY => $line) {
            $y = [];

            foreach (str_split((string) $line) as $keyX => $letter) {
                $position = new Position($keyY, $keyX, $letter);

                if (self::START === $letter) {
                    $this->start = $position;
                } elseif (self::END === $letter) {
                    $this->end = $position;
                }

                $y[$keyX] = $position;
            }

            $this->positions[$keyY] = $y;
        }

        $terrainCost = new TerrainCost($this->positions);
        $this->domainLogic = new DomainLogic($terrainCost);
        $this->aStar = new AStar($this->domainLogic);
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        $fewestMoves = $this->aStar->run($this->start, $this->end);

        return \count($fewestMoves) - 1;
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        $paths = [];

        foreach ($this->domainLogic->lowestPositions as $position) {
            $run = $this->aStar->run(
                $position,
                $this->domainLogic->end,
            );

            if (!empty($run)) {
                $paths[\count($run) - 1] = $run;
            }
        }

        ksort($paths);

        return array_key_first($paths);
    }
}
