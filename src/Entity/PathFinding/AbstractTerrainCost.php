<?php

namespace App\Entity\PathFinding;

use App\Entity\Year2023\Day10\Position;

abstract class AbstractTerrainCost implements TerrainCostInterface
{
    public const int INFINITE = PHP_INT_MAX;

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

    public function getCost(Position $from, Position $to): int
    {
        return 0;
    }
}
