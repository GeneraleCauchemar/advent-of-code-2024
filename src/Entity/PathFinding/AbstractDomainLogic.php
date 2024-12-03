<?php

namespace App\Entity\PathFinding;

use JMGQ\AStar\DomainLogicInterface;

abstract class AbstractDomainLogic implements DomainLogicInterface
{
    protected array $positions;

    public function __construct(protected TerrainCostInterface $terrainCost)
    {
    }

    public function getAdjacentNodes(mixed $node): iterable
    {
        $adjacentNodes = [];

        [$startingRow, $endingRow, $startingColumn, $endingColumn] = $this->calculateAdjacentBoundaries($node);

        for ($row = $startingRow; $row <= $endingRow; $row++) {
            for ($column = $startingColumn; $column <= $endingColumn; $column++) {
                // We can't move diagonally
                if ($row === $node->row || $column === $node->column) {
                    $adjacentNode = $this->positions[$row][$column];

                    if (
                        $this->terrainCost->canMoveTo($node, $adjacentNode)
                        && !$node->isEqualTo($adjacentNode)
                    ) {
                        $adjacentNodes[] = $adjacentNode;
                    }
                }
            }
        }

        return $adjacentNodes;
    }

    public function calculateRealCost(mixed $node, mixed $adjacent): float|int
    {
        return 0;
    }

    public function calculateEstimatedCost(mixed $fromNode, mixed $toNode): float|int
    {
        return 0;
    }

    protected function calculateAdjacentBoundaries(PositionInterface $position): array
    {
        return [
            0 === $position->row ? 0 : $position->row - 1,
            $this->terrainCost->getTotalRows() - 1 === $position->row ? $position->row : $position->row + 1,
            0 === $position->column ? 0 : $position->column - 1,
            $this->terrainCost->getTotalColumns() - 1 === $position->column ? $position->column : $position->column + 1,
        ];
    }

    protected function calculateEuclideanDistance(PositionInterface $a, PositionInterface $b): float
    {
        $rowFactor = ($a->row - $b->row) ** 2;
        $columnFactor = ($a->column - $b->column) ** 2;

        return sqrt($rowFactor + $columnFactor);
    }
}
