<?php

namespace App\Entity\Year2024\Day10;

use App\Entity\PathFinding\AbstractDomainLogic;
use App\Entity\PathFinding\PositionInterface;
use Webmozart\Assert\Assert;

class DomainLogic extends AbstractDomainLogic
{
    public function __construct(array $map)
    {
        parent::__construct($map);
    }

    protected function canMoveTo(PositionInterface $node, PositionInterface $adjacentNode): bool
    {
        Assert::isInstanceOf($node, Position::class);
        Assert::isInstanceOf($adjacentNode, Position::class);

        return $node->height === ($adjacentNode->height - 1);
    }

    protected function getTotalRows(): int
    {
        return \count($this->positions);
    }

    protected function getTotalColumns(): int
    {
        return \count($this->positions[0]);
    }
}
