<?php

namespace App\Entity\Year2024\Day04;

use App\Entity\PathFinding\PositionInterface;
use App\Service\CompassHelper;

readonly class DomainLogic
{
    public function __construct(private array $map)
    {
    }

    public function getAdjacentNodes(Position $from, string $letter, bool $isPartOne = true): array
    {
        $adjacentNodes = [];
        [$startingRow, $endingRow, $startingColumn, $endingColumn] = $this->calculateAdjacentBoundaries($from);

        for ($row = $startingRow; $row <= $endingRow; $row++) {
            for ($column = $startingColumn; $column <= $endingColumn; $column++) {
                /** @var Position $adjacentNode */
                $adjacentNode = $this->map[$row][$column];

                if (!$from->isEqualTo($adjacentNode)) {
                    $direction = $this->getDirection($from, $adjacentNode);

                    if (
                        $letter === $adjacentNode->letter
                        && ($isPartOne || CompassHelper::isOnADiagonalAxis($direction))
                    ) {
                        $adjacentNodes[$direction] = $adjacentNode;
                    }
                }
            }
        }

        return $adjacentNodes;
    }

    public function getNextPositionForSameDirection(Position $position, string $direction): ?Position
    {
        [$x, $y] = CompassHelper::getDiffFromDirection($direction, $position->column, $position->row);
        $next = $this->map[$y][$x] ?? null;

        if ($position->getNextLetter() === $next?->letter) {
            return $next;
        }

        return null;
    }

    private function calculateAdjacentBoundaries(PositionInterface $position): array
    {
        return [
            max(0, $position->row - 1),
            \count($this->map) - 1 === $position->row ? $position->row : $position->row + 1,
            max(0, $position->column - 1),
            \count($this->map[0]) - 1 === $position->column ? $position->column : $position->column + 1,
        ];
    }

    private function getDirection(Position $from, Position $to): ?string
    {
        return CompassHelper::getDirectionFromDiff($to->column - $from->column, $to->row - $from->row);
    }
}
