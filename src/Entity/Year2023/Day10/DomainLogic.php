<?php

namespace App\Entity\Year2023\Day10;

use App\Entity\PathFinding\AbstractDomainLogic;
use App\Entity\PathFinding\TerrainCostInterface;

class DomainLogic extends AbstractDomainLogic
{
    public function __construct(protected TerrainCostInterface $terrainCost)
    {
        parent::__construct($this->terrainCost);

        $this->positions = $this->terrainCost->positions;
    }

    #[\Override]
    public function calculateEstimatedCost(mixed $fromNode, mixed $toNode): float|int
    {
        return $this->calculateEuclideanDistance($fromNode, $toNode);
    }
}
