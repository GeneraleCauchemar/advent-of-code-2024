<?php

namespace App\Entity\PathFinding;

use App\Entity\Year2023\Day10\Position;

interface TerrainCostInterface
{
    public function getTotalRows(): int;

    public function getTotalColumns(): int;

    public function getCost(Position $from, Position $to): int;
}
