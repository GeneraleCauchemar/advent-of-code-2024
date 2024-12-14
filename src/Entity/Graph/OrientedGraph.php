<?php

namespace App\Entity\Graph;

use App\Exception\NodeAlreadyInGraphException;

class OrientedGraph
{
    public array $nodes = [];

    /**
     * @throws NodeAlreadyInGraphException
     */
    public function addNode(NodeInterface $node): void
    {
        if ($this->getNode($node->id) instanceof NodeInterface) {
            throw new NodeAlreadyInGraphException();
        }

        $this->nodes[$node->id] = $node;
    }

    public function getNode(int $id): ?NodeInterface
    {
        return $this->nodes[$id] ?? null;
    }

    public function addEdge(NodeInterface $from, NodeInterface $to): void
    {
        $from->addEdgeTo($to);
    }
}
