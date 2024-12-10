<?php

namespace App\Entity\PathFinding;

use JMGQ\AStar\DomainLogicInterface;

abstract class AbstractDomainLogic implements DomainLogicInterface
{
    public function __construct(
        protected array $positions,
        protected ?TerrainCostInterface $terrainCost = null,
        protected bool $canMoveDiagonnally = false
    ) {
    }

    public function getAdjacentNodes(mixed $node): iterable
    {
        $adjacentNodes = [];

        [$startingRow, $endingRow, $startingColumn, $endingColumn] = $this->calculateAdjacentBoundaries($node);

        for ($row = $startingRow; $row <= $endingRow; $row++) {
            for ($column = $startingColumn; $column <= $endingColumn; $column++) {
                // We can't move diagonally
                if ($this->canMoveDiagonnally || ($row === $node->row || $column === $node->column)) {
                    $adjacentNode = $this->positions[$row][$column];

                    if ($this->canMoveTo($node, $adjacentNode)) {
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
            $this->getTotalRows() - 1 === $position->row ? $position->row : $position->row + 1,
            0 === $position->column ? 0 : $position->column - 1,
            $this->getTotalColumns() - 1 === $position->column ? $position->column : $position->column + 1,
        ];
    }

    protected function calculateEuclideanDistance(PositionInterface $a, PositionInterface $b): float
    {
        $rowFactor = ($a->row - $b->row) ** 2;
        $columnFactor = ($a->column - $b->column) ** 2;

        return sqrt($rowFactor + $columnFactor);
    }

    protected function canMoveTo(PositionInterface $node, PositionInterface $adjacentNode): bool
    {
        return $this->terrainCost->canMoveTo($node, $adjacentNode)
            && !$node->isEqualTo($adjacentNode);
    }

    protected function getTotalRows(): int
    {
        return $this->terrainCost->getTotalRows();
    }

    protected function getTotalColumns(): int
    {
        return $this->terrainCost->getTotalColumns();
    }
}
