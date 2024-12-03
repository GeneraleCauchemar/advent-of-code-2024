<?php

namespace App\Entity\Year2022\Day12;

use App\Entity\PathFinding\AbstractDomainLogic;
use App\Entity\PathFinding\TerrainCostInterface;

class DomainLogic extends AbstractDomainLogic
{
    private const int LOWEST = 1;

    public array $lowestPositions;
    public Position $end;

    public function __construct(protected TerrainCostInterface $terrainCost)
    {
        parent::__construct($this->terrainCost);

        $this->positions = $this->terrainCost->positions;

        $this->determineLowestPositionsAndEndPoint();
    }

    private function determineLowestPositionsAndEndPoint(): void
    {
        for ($column = 0; $column < $this->terrainCost->getTotalColumns(); $column++) {
            for ($row = 0; $row < $this->terrainCost->getTotalRows(); $row++) {
                /** @var Position $position */
                $position = $this->positions[$row][$column];

                if (self::LOWEST === $position->elevation) {
                    $this->lowestPositions[] = $position;
                }

                if ($position->isEndingPoint) {
                    $this->end = $position;
                }
            }
        }
    }
}
