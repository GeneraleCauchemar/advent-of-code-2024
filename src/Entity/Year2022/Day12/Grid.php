<?php

namespace App\Entity\Year2022\Day12;

class Grid
{
    private array $nodes = [];

    public function __construct($grid)
    {
        foreach ($grid as $y => $cols) {
            foreach ($cols as $x => $z) {
                $this->nodes[$y][$x] = new Node($y, $x, $z);
            }
        }
    }

    public function getPoint(int $y, int $x)
    {
        return $this->nodes[$y][$x] ?? null;
    }

    public function getAdjacent(Node $node): array
    {
        $result = [];
        $x = $node->getX();
        $y = $node->getY();

        $neighbourLocations = [
            [$y - 1, $x],
            [$y + 1, $x],
            [$y, $x - 1],
            [$y, $x + 1],
        ];

        foreach ($neighbourLocations as $location) {
            [$y, $x] = $location;
            $neighbour = $this->getPoint($y, $x);

            if ($neighbour && $this->canMoveTo($node, $neighbour)) {
                $result[] = $neighbour;
            }
        }

        return $result;
    }

    private function canMoveTo(Node $from, Node $to): bool
    {
        // Starting from the end
        return $from->getZ() <= $to->getZ() || ($from->getZ() - 1) === $to->getZ();
    }
}
