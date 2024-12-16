<?php

namespace App\Entity\Year2024\Day16;

use App\Entity\PathFinding\AStar\AbstractDomainLogic;
use App\Entity\PathFinding\AStar\NodeInterface;

class DomainLogic extends AbstractDomainLogic
{
    public function canMoveTo(NodeInterface $node, NodeInterface $neighbour): bool
    {
        /** @var Node $neighbour */
        return Node::WALL !== $neighbour->symbol;
    }
}
