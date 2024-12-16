<?php

namespace App\Entity\Year2024\Day16;

use App\Entity\PathFinding\AbstractTerrainCost;
use App\Entity\PathFinding\PositionInterface;

class TerrainCost extends AbstractTerrainCost
{
    public function __construct(public array $positions)
    {
        parent::__construct($this->positions);
    }

    public function canMoveTo(PositionInterface $from, PositionInterface $to): bool
    {
        /** @var Node $to */
        /** @var Node $from */
        return !$to->isStart() && !$to->isWall();
    }
}
