<?php

namespace App\Entity\PathFinding;

interface TerrainCostInterface
{
    public function getTotalRows(): int;

    public function getTotalColumns(): int;

    public function getCost(PositionInterface $from, PositionInterface $to): int;

    public function canMoveTo(PositionInterface $from, PositionInterface $to): bool;
}
