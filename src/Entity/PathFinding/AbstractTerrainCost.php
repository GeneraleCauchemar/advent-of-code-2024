<?php

namespace App\Entity\PathFinding;

abstract class AbstractTerrainCost implements TerrainCostInterface
{
    public function __construct(public array $positions)
    {
    }

    public function getTotalRows(): int
    {
        return \count($this->positions);
    }

    public function getTotalColumns(): int
    {
        return \count($this->positions[0]);
    }

    public function getCost(PositionInterface $from, PositionInterface $to): int
    {
        return 0;
    }
}
