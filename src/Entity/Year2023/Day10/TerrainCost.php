<?php

namespace App\Entity\Year2023\Day10;

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
        /** @var Position $to */
        /** @var Position $from */
        return !$to->isGround && !$to->isStartingPoint && $to->isOrientedTowards($from);
    }
}
