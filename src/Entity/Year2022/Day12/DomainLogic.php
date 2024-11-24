<?php

namespace App\Entity\Year2022\Day12;

use App\Entity\PathFinding\AbstractDomainLogic;
use App\Entity\PathFinding\TerrainCostInterface;

class DomainLogic extends AbstractDomainLogic
{
    private const int LOWEST = 1;

    public array $lowestPositions;
    public Position $end;
    private array $positions;

    public function __construct(protected TerrainCostInterface $terrainCost)
    {
        parent::__construct($this->terrainCost);

        $this->positions = $this->terrainCost->positions;

        $this->determineLowestPositionsAndEndPoint();
    }

    #[\Override]
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

    #[\Override]
    public function calculateRealCost(mixed $node, mixed $adjacent): float|int
    {
        return 0;
    }

    #[\Override]
    public function calculateEstimatedCost(mixed $fromNode, mixed $toNode): float|int
    {
        return 0;
    }

    private function determineLowestPositionsAndEndPoint(): void
    {
        for ($column = 0; $column < $this->terrainCost->getTotalColumns(); $column++) {
            for ($row = 0; $row < $this->terrainCost->getTotalRows(); $row++) {
                /** @var Position $position */
                $position = $this->positions[$row][$column];

                if (self::LOWEST === $position->elevation) {
                    $this->lowestPositions[] = $position;
                }

                if ($position->isEndingPoint) {
                    $this->end = $position;
                }
            }
        }
    }
}
