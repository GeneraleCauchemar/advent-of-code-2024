<?php

namespace App\Entity\Year2022\Day12;

use App\Entity\PathFinding\AbstractTerrainCost;

class TerrainCost extends AbstractTerrainCost
{
    public function __construct(public array $positions)
    {
        parent::__construct($this->positions);
    }

    public function canMoveTo(Position $from, Position $to): bool
    {
        /**
         * To avoid needing to get out your climbing gear, the elevation of
         * the destination square can be at most one higher than the elevation
         * of your current square; that is, if your current elevation is m, you
         * could step to elevation n, but not to elevation o. (This also means
         * that the elevation of the destination square can be much lower than
         * the elevation of your current square.)
         */
        return ($to->elevation - 1) === $from->elevation || $to->elevation <= $from->elevation;
    }
}
