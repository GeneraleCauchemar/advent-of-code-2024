<?php

namespace App\Entity\PathFinding;

use JMGQ\AStar\DomainLogicInterface;

abstract class AbstractDomainLogic implements DomainLogicInterface
{
    public function __construct(protected TerrainCostInterface $terrainCost)
    {
    }

    protected function calculateAdjacentBoundaries(PositionInterface $position): array
    {
        return [
            0 === $position->row ? 0 : $position->row - 1,
            $this->terrainCost->getTotalRows() - 1 === $position->row ? $position->row : $position->row + 1,
            0 === $position->column ? 0 : $position->column - 1,
            $this->terrainCost->getTotalColumns() - 1 === $position->column ? $position->column : $position->column + 1,
        ];
    }
}
