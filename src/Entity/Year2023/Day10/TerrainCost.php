<?php

namespace App\Entity\Year2023\Day10;

use App\Entity\PathFinding\AbstractTerrainCost;

class TerrainCost extends AbstractTerrainCost
{
    public function __construct(public array $positions)
    {
        parent::__construct($this->positions);
    }

    #[\Override]
    public function getCost(Position $from, Position $to): int
    {
        if ($to->isGround || $to->isStartingPoint || !$to->isOrientedTowards($from)) {
            return self::INFINITE;
        }

        return 0;
    }
}
