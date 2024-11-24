<?php

namespace App\Entity\Year2023\Day10;

use App\Entity\PathFinding\AbstractDomainLogic;
use App\Entity\PathFinding\AbstractTerrainCost;
use App\Entity\PathFinding\TerrainCostInterface;

class DomainLogic extends AbstractDomainLogic
{
    private array $positions;

    public function __construct(protected TerrainCostInterface $terrainCost)
    {
        parent::__construct($this->terrainCost);

        $this->positions = $this->terrainCost->positions;
    }

    #[\Override]
    public function getAdjacentNodes(mixed $node): iterable
    {
        $adjacentNodes = [];

        [$startingRow, $endingRow, $startingColumn, $endingColumn] = $this->calculateAdjacentBoundaries($node);

        for ($row = $startingRow; $row <= $endingRow; $row++) {
            for ($column = $startingColumn; $column <= $endingColumn; $column++) {
                $adjacentNode = $this->positions[$row][$column];

                if (!$node->isEqualTo($adjacentNode)) {
                    $adjacentNodes[] = $adjacentNode;
                }
            }
        }

        return $adjacentNodes;
    }

    #[\Override]
    public function calculateRealCost(mixed $node, mixed $adjacent): float|int
    {
        if ($node->isAdjacentTo($adjacent)) {
            return $this->terrainCost->getCost($node, $adjacent);
        }

        return AbstractTerrainCost::INFINITE;
    }

    #[\Override]
    public function calculateEstimatedCost(mixed $fromNode, mixed $toNode): float|int
    {
        if ($fromNode === $toNode) {
            return PHP_INT_MAX;
        }

        return $this->euclideanDistance($fromNode, $toNode);
    }

    private function euclideanDistance(Position $a, Position $b): float
    {
        $rowFactor = ($a->row - $b->row) ** 2;
        $columnFactor = ($a->column - $b->column) ** 2;

        return sqrt($rowFactor + $columnFactor);
    }
}
