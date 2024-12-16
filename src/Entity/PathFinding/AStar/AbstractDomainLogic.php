<?php

declare(strict_types=1);

namespace App\Entity\PathFinding\AStar;

use App\Entity\Vector2D;
use App\Enum\Direction;

abstract class AbstractDomainLogic implements DomainLogicInterface
{
    public function __construct(
        private readonly array $grid,
        private readonly bool $penaliseDirectionChange = false,
        private readonly int $penalty = 0,
    ) {
    }

    public function getGrid(): array
    {
        return $this->grid;
    }

    public function getPenaliseDirectionChange(): bool
    {
        return $this->penaliseDirectionChange;
    }

    public function getPenalty(): int
    {
        return $this->penalty;
    }

    public function getAdjacentNodes(NodeInterface $node): array
    {
        $result = [];

        foreach (Direction::cases() as $direction) {
            $vector = $direction->getVector();
            $neighbour = $this->getNodeFromVector($vector, $node);

            if (
                $neighbour instanceof NodeInterface
                && !$node->isEqualTo($neighbour)
                && $this->canMoveTo($node, $neighbour)
            ) {
                $result[] = $neighbour;
            }
        }

        return $result;
    }

    public function getRealCost(NodeInterface $from, NodeInterface $to): int|float
    {
        return 1;
    }

    public function getHeuristic(NodeInterface $from, NodeInterface $to): float|int
    {
        return $this->computeManhattanDistance($from, $to);
    }

    protected function computeEuclideanDistance(NodeInterface $a, NodeInterface $b): float
    {
        $rowFactor = ($a->getY() - $b->getY()) ** 2;
        $columnFactor = ($a->getX() - $b->getX()) ** 2;

        return sqrt($rowFactor + $columnFactor);
    }

    protected function computeManhattanDistance(NodeInterface $from, NodeInterface $to): float|int
    {
        return abs($from->getX() - $to->getX()) + abs($from->getY() - $to->getY());
    }

    private function getNodeFromVector(Vector2D $direction, NodeInterface $node): ?NodeInterface
    {
        $nodeAsVector = new Vector2D($node->getX(), $node->getY());
        $adjacentAsVector = $nodeAsVector->add($direction);

        return $this->grid[$adjacentAsVector->y][$adjacentAsVector->x] ?? null;
    }

    abstract public function canMoveTo(NodeInterface $node, NodeInterface $neighbour): bool;
}
