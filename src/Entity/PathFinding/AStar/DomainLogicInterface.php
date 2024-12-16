<?php

declare(strict_types=1);

namespace App\Entity\PathFinding\AStar;

interface DomainLogicInterface
{
    public function getGrid(): array;

    public function getPenaliseDirectionChange(): bool;

    public function getPenalty(): int;

    public function getAdjacentNodes(NodeInterface $node): array;

    public function getRealCost(NodeInterface $from, NodeInterface $to): int|float;

    public function getHeuristic(NodeInterface $from, NodeInterface $to): float|int;

    public function canMoveTo(NodeInterface $node, NodeInterface $neighbour): bool;
}
